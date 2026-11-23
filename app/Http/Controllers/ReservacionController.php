<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Huesped;
use App\Models\Reservacion;
use App\Services\CurrencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReservacionController extends Controller
{
    public function index(Request $request, CurrencyService $currencyService)
    {
        $filtroEstado = $request->query('estado', 'todas');

        // Mejora de rendimiento: se filtra en la base de datos y se muestran 25 por página
        // (antes se traían TODAS las reservaciones y se filtraban en PHP)
        $reservaciones = Reservacion::with(['habitacion', 'huesped'])
            ->when($filtroEstado !== 'todas', fn ($q) => $q->where('estado', $filtroEstado))
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        // Cuántas reservaciones hay de cada estado, en una sola consulta
        $conteos = Reservacion::selectRaw('estado, count(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $habitaciones = Habitacion::where('estado', '!=', 'mantenimiento')->orderBy('numero')->get();
        $huespedes = Huesped::orderBy('nombre_completo')->get();
        $tasas = $currencyService->obtenerTasasDesdeMxn();

        // Valores preseleccionados si viene desde "Disponibilidad"
        $preseleccion = [
            'habitacion_id' => $request->query('habitacion_id'),
            'fecha_entrada' => $request->query('fecha_entrada', now()->format('Y-m-d')),
            'fecha_salida' => $request->query('fecha_salida', now()->addDays(2)->format('Y-m-d')),
            'capacidad' => $request->query('capacidad', 2),
        ];

        return view('reservaciones.index', compact(
            'reservaciones', 'conteos', 'filtroEstado', 'habitaciones',
            'huespedes', 'tasas', 'preseleccion'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'habitacion_id' => 'required|exists:habitaciones,id',
            'fecha_entrada' => 'required|date|after_or_equal:today',
            'fecha_salida' => 'required|date|after:fecha_entrada',
            'numero_huespedes' => 'required|integer|min:1|max:8',
            'modo_huesped' => 'required|in:existente,nuevo',
            'huesped_id' => 'required_if:modo_huesped,existente|nullable|exists:huespedes,id',
            'nombre_completo' => 'required_if:modo_huesped,nuevo|nullable|string|max:100',
            'tipo_documento' => 'required_if:modo_huesped,nuevo|nullable|in:INE,Pasaporte,Licencia,Otro',
            'documento_identidad' => 'required_if:modo_huesped,nuevo|nullable|string|max:50|unique:huespedes,documento_identidad',
            'telefono' => ['required_if:modo_huesped,nuevo', 'nullable', 'regex:/^[0-9 +()-]{7,25}$/'],
            'email' => 'nullable|email|max:150',
        ], [
            'fecha_entrada.after_or_equal' => 'La fecha de llegada no puede ser anterior a hoy.',
            'fecha_salida.after' => 'La fecha de salida debe ser posterior a la llegada.',
            'documento_identidad.unique' => 'Ya existe un huésped registrado con ese documento.',
            'telefono.regex' => 'El teléfono solo puede tener números, espacios, guiones o paréntesis (mínimo 7).',
            'nombre_completo.required_if' => 'Ingresa el nombre completo del nuevo huésped.',
            'documento_identidad.required_if' => 'Ingresa el número de identificación del huésped.',
            'telefono.required_if' => 'Ingresa el teléfono del huésped.',
        ]);

        $habitacion = Habitacion::findOrFail($request->habitacion_id);

        if ($request->numero_huespedes > $habitacion->capacidad) {
            return back()->withErrors([
                'numero_huespedes' => "La habitación {$habitacion->numero} permite máximo {$habitacion->capacidad} huéspedes.",
            ])->withInput();
        }

        // Validar que no existan reservaciones traslapadas en ese rango
        $traslape = Reservacion::where('habitacion_id', $habitacion->id)
            ->whereIn('estado', ['confirmada', 'en_casa'])
            ->where('fecha_entrada', '<', $request->fecha_salida)
            ->where('fecha_salida', '>', $request->fecha_entrada)
            ->exists();

        if ($traslape) {
            return back()->withErrors([
                'habitacion_id' => "La habitación {$habitacion->numero} ya tiene una reservación activa en esas fechas.",
            ])->withInput();
        }

        DB::transaction(function () use ($request, $habitacion) {
            if ($request->modo_huesped === 'nuevo') {
                $siguienteNum = Huesped::count() + 1;
                $huesped = Huesped::create([
                    'codigo_huesped' => 'G-' . str_pad($siguienteNum, 2, '0', STR_PAD_LEFT),
                    'nombre_completo' => $request->nombre_completo,
                    'tipo_documento' => $request->tipo_documento,
                    'documento_identidad' => $request->documento_identidad,
                    'telefono' => $request->telefono,
                    'email' => $request->email,
                ]);
                $huespedId = $huesped->id;
            } else {
                $huespedId = $request->huesped_id;
            }

            $totalMxn = Reservacion::calcularTotal($habitacion->precio_noche, $request->fecha_entrada, $request->fecha_salida);

            $ultimoId = Reservacion::max('id') ?? 1000;
            $folio = 'R-' . (1000 + $ultimoId);

            Reservacion::create([
                'folio' => $folio,
                'habitacion_id' => $habitacion->id,
                'huesped_id' => $huespedId,
                'user_id' => Auth::id(),
                'fecha_entrada' => $request->fecha_entrada,
                'fecha_salida' => $request->fecha_salida,
                'numero_huespedes' => $request->numero_huespedes,
                'total_mxn' => $totalMxn,
                'estado' => 'confirmada',
            ]);

            if ($habitacion->estado === 'disponible') {
                $habitacion->update(['estado' => 'reservada']);
            }
        });

        return redirect()->route('reservaciones.index')->with('success', 'Reservación registrada correctamente.');
    }

    public function checkIn(Reservacion $reservacion)
    {
        if ($reservacion->estado !== 'confirmada') {
            return back()->withErrors(['estado' => 'Solo se puede hacer Check-in a reservaciones confirmadas.']);
        }

        $reservacion->update(['estado' => 'en_casa']);
        $reservacion->habitacion->update(['estado' => 'ocupada']);

        return back()->with('success', "Check-in realizado para {$reservacion->folio}. Habitación {$reservacion->habitacion->numero} ocupada.");
    }

    public function checkOut(Reservacion $reservacion)
    {
        if ($reservacion->estado !== 'en_casa') {
            return back()->withErrors(['estado' => 'Solo se puede hacer Check-out a huéspedes en casa.']);
        }

        $reservacion->update(['estado' => 'finalizada']);
        $reservacion->habitacion->update(['estado' => 'limpieza']);

        return back()->with('success', "Check-out finalizado para {$reservacion->folio}. Habitación {$reservacion->habitacion->numero} enviada a limpieza.");
    }

    public function cancelar(Reservacion $reservacion)
    {
        if ($reservacion->estado !== 'confirmada') {
            return back()->withErrors(['estado' => 'Solo se pueden cancelar reservaciones confirmadas antes del Check-in.']);
        }

        $reservacion->update(['estado' => 'cancelada']);

        // Si no tiene otra reserva confirmada, liberar habitación
        $tieneOtra = Reservacion::where('habitacion_id', $reservacion->habitacion_id)
            ->where('id', '!=', $reservacion->id)
            ->whereIn('estado', ['confirmada', 'en_casa'])
            ->exists();

        if (!$tieneOtra) {
            $reservacion->habitacion->update(['estado' => 'disponible']);
        }

        return back()->with('success', "Reservación {$reservacion->folio} cancelada.");
    }
}