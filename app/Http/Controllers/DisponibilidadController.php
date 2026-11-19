<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class DisponibilidadController extends Controller
{
    public function index(Request $request, CurrencyService $currencyService)
    {
        $todasHabitaciones = Habitacion::with(['reservaciones' => function ($q) {
            $q->whereIn('estado', ['en_casa', 'confirmada'])->with('huesped');
        }])->orderBy('piso')->orderBy('numero')->get();

        // Métricas superiores
        $vendibles = $todasHabitaciones->whereNotIn('estado', ['mantenimiento', 'limpieza'])->count();
        $ocupadas = $todasHabitaciones->where('estado', 'ocupada')->count();
        $disponiblesHoy = $todasHabitaciones->where('estado', 'disponible')->count();
        $reservadasHoy = $todasHabitaciones->where('estado', 'reservada')->count();
        $limpiezaCount = $todasHabitaciones->where('estado', 'limpieza')->count();
        $mantenimientoCount = $todasHabitaciones->where('estado', 'mantenimiento')->count();
        $porcentajeOcupacion = $vendibles > 0 ? round(($ocupadas / $vendibles) * 100) : 0;

        // Consulta de disponibilidad por fechas, tipo y huéspedes
        $habitacionesLibres = null;
        $fechaEntrada = $request->input('fecha_entrada', now()->format('Y-m-d'));
        $fechaSalida = $request->input('fecha_salida', now()->addDays(2)->format('Y-m-d'));
        $tipo = $request->input('tipo', 'Todas');
        $capacidad = (int) $request->input('capacidad', 2);

        if ($request->has('consultar')) {
            $request->validate([
                'fecha_entrada' => 'required|date',
                'fecha_salida' => 'required|date|after:fecha_entrada',
                'capacidad' => 'required|integer|min:1|max:6',
            ], [
                'fecha_salida.after' => 'La fecha de salida debe ser posterior a la fecha de llegada.',
            ]);

            $habitacionesLibres = $this->consultarDisponibilidad($fechaEntrada, $fechaSalida, $capacidad, $tipo);
        }

        $tasas = $currencyService->obtenerTasasDesdeMxn();

        return view('disponibilidad.index', compact(
            'todasHabitaciones', 'ocupadas', 'vendibles', 'disponiblesHoy',
            'reservadasHoy', 'limpiezaCount', 'mantenimientoCount',
            'porcentajeOcupacion', 'habitacionesLibres', 'fechaEntrada',
            'fechaSalida', 'tipo', 'capacidad', 'tasas'
        ));
    }

    public function consultarDisponibilidad(string $fechaEntrada, string $fechaSalida, int $capacidad, string $tipo = 'Todas')
    {
        $query = Habitacion::where('estado', '!=', 'mantenimiento')
            ->where('capacidad', '>=', $capacidad)
            ->whereDoesntHave('reservaciones', function ($q) use ($fechaEntrada, $fechaSalida) {
                $q->whereIn('estado', ['confirmada', 'en_casa'])
                  ->where('fecha_entrada', '<', $fechaSalida)
                  ->where('fecha_salida', '>', $fechaEntrada);
            });

        if ($tipo !== 'Todas') {
            $query->where('tipo', $tipo);
        }

        return $query->orderBy('piso')->orderBy('numero')->get();
    }
}