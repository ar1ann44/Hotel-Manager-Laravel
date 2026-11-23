<?php

namespace App\Http\Controllers;

use App\Models\Habitacion;
use App\Models\Reservacion;
use Illuminate\Http\Request;

/**
 * API de consulta (solo lectura). Todas las respuestas son JSON.
 */
class ApiController extends Controller
{
    // GET /api/habitaciones
    public function habitaciones()
    {
        $habitaciones = Habitacion::orderBy('numero')
            ->get(['id', 'numero', 'piso', 'tipo', 'capacidad', 'precio_noche', 'estado']);

        return response()->json([
            'total' => $habitaciones->count(),
            'habitaciones' => $habitaciones,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // GET /api/disponibilidad?fecha_entrada=2026-12-01&fecha_salida=2026-12-03&capacidad=2
    public function disponibilidad(Request $request)
    {
        $datos = $request->validate([
            'fecha_entrada' => 'required|date',
            'fecha_salida' => 'required|date|after:fecha_entrada',
            'capacidad' => 'required|integer|min:1|max:8',
            'tipo' => 'nullable|in:Todas,Estándar,Doble,Suite',
        ], [
            'fecha_salida.after' => 'La fecha de salida debe ser posterior a la llegada.',
        ]);

        $libres = Habitacion::disponibles(
            $datos['fecha_entrada'],
            $datos['fecha_salida'],
            (int) $datos['capacidad'],
            $datos['tipo'] ?? 'Todas'
        );

        return response()->json([
            'fecha_entrada' => $datos['fecha_entrada'],
            'fecha_salida' => $datos['fecha_salida'],
            'total' => $libres->count(),
            'habitaciones' => $libres->map(fn ($h) => [
                'numero' => $h->numero,
                'tipo' => $h->tipo,
                'capacidad' => $h->capacidad,
                'precio_noche' => $h->precio_noche,
            ]),
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // GET /api/reservaciones/R-1001
    public function reservacion(string $folio)
    {
        $reservacion = Reservacion::with(['habitacion', 'huesped'])
            ->where('folio', $folio)
            ->first();

        if (! $reservacion) {
            return response()->json(['mensaje' => "No existe la reservación {$folio}."], 404, [], JSON_UNESCAPED_UNICODE);
        }

        // Solo se regresan datos básicos (sin teléfono ni documento del huésped)
        return response()->json([
            'folio' => $reservacion->folio,
            'estado' => $reservacion->estado,
            'habitacion' => $reservacion->habitacion->numero,
            'huesped' => $reservacion->huesped->nombre_completo,
            'fecha_entrada' => $reservacion->fecha_entrada,
            'fecha_salida' => $reservacion->fecha_salida,
            'numero_huespedes' => $reservacion->numero_huespedes,
            'total_mxn' => $reservacion->total_mxn,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
