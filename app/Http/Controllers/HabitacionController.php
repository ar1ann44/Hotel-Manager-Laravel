<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Services\CurrencyService;
use Illuminate\Http\Request;

class HabitacionController extends Controller
{
    public function index(Request $request, CurrencyService $currencyService)
    {
        $filtroEstado = $request->query('estado', 'todas');

        $todas = Habitacion::with(['reservaciones' => function ($q) {
            $q->whereIn('estado', ['en_casa', 'confirmada'])->with('huesped');
        }])->orderBy('piso')->orderBy('numero')->get();

        $habitaciones = $filtroEstado === 'todas'
            ? $todas
            : $todas->where('estado', $filtroEstado);

        $tasas = $currencyService->obtenerTasasDesdeMxn();

        return view('habitaciones.index', compact('habitaciones', 'todas', 'filtroEstado', 'tasas'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'numero' => 'required|string|max:10|unique:habitaciones,numero',
            'piso' => 'required|integer|min:1|max:10',
            'tipo' => 'required|in:Estándar,Doble,Suite',
            'capacidad' => 'required|integer|min:1|max:8',
            'precio_noche' => 'required|numeric|min:100',
        ]);

        Habitacion::create($data + ['estado' => 'disponible']);

        return back()->with('success', 'Habitación ' . $data['numero'] . ' creada correctamente.');
    }

    public function update(Request $request, Habitacion $habitacion)
    {
        $data = $request->validate([
            'tipo' => 'required|in:Estándar,Doble,Suite',
            'capacidad' => 'required|integer|min:1|max:8',
            'precio_noche' => 'required|numeric|min:100',
        ]);

        $habitacion->update($data);

        return back()->with('success', 'Tarifa y datos de la habitación ' . $habitacion->numero . ' actualizados.');
    }

    public function cambiarEstado(Request $request, Habitacion $habitacion)
    {
        $request->validate([
            'estado' => 'required|in:disponible,limpieza,mantenimiento',
        ]);

        if (in_array($habitacion->estado, ['ocupada', 'reservada'])) {
            return back()->withErrors(['estado' => 'No se puede cambiar manualmente una habitación ocupada o reservada.']);
        }

        $habitacion->update(['estado' => $request->estado]);

        return back()->with('success', 'Estado de habitación ' . $habitacion->numero . ' cambiado a ' . $request->estado . '.');
    }
}