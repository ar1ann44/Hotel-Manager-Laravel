<?php

namespace App\Http\Controllers;

use App\Models\Huesped;
use Illuminate\Http\Request;

class HuespedController extends Controller
{
    public function index(Request $request)
    {
        $busqueda = trim((string) $request->query('q', ''));

        $huespedes = Huesped::with(['reservaciones.habitacion'])
            ->when($busqueda !== '', function ($q) use ($busqueda) {
                $q->where('nombre_completo', 'like', "%{$busqueda}%")
                  ->orWhere('email', 'like', "%{$busqueda}%")
                  ->orWhere('codigo_huesped', 'like', "%{$busqueda}%")
                  ->orWhere('documento_identidad', 'like', "%{$busqueda}%");
            })
            ->orderByDesc('id')
            ->paginate(25)            // Mejora de rendimiento: 25 huéspedes por página
            ->withQueryString();

        $totalRegistrados = Huesped::count();
        $enCasaCount = Huesped::whereHas('reservaciones', fn($q) => $q->where('estado', 'en_casa'))->count();
        $conReservaCount = Huesped::whereHas('reservaciones', fn($q) => $q->where('estado', 'confirmada'))->count();

        return view('huespedes.index', compact(
            'huespedes', 'busqueda', 'totalRegistrados', 'enCasaCount', 'conReservaCount'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre_completo' => 'required|string|max:100',
            'tipo_documento' => 'required|in:INE,Pasaporte,Licencia,Otro',
            'documento_identidad' => 'required|string|max:50|unique:huespedes,documento_identidad',
            'telefono' => ['required', 'regex:/^[0-9 +()-]{7,25}$/'],
            'email' => 'nullable|email|max:150',
        ], [
            'documento_identidad.unique' => 'Ya existe un huésped registrado con ese documento.',
            'telefono.regex' => 'El teléfono solo puede tener números, espacios, guiones o paréntesis (mínimo 7).',
        ]);

        $siguienteNum = Huesped::count() + 1;
        $data['codigo_huesped'] = 'G-' . str_pad($siguienteNum, 2, '0', STR_PAD_LEFT);

        Huesped::create($data);

        return back()->with('success', "Huésped {$data['nombre_completo']} ({$data['codigo_huesped']}) registrado.");
    }
}