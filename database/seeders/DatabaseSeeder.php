<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Habitacion;
use App\Models\Huesped;
use App\Models\Reservacion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Usuarios del sistema (Administrador y Recepcionista)
        $admin = User::create([
            'name' => 'Administrador',
            'apellidos' => 'Penglai',
            'email' => 'admin@penglai.com',
            'password' => Hash::make('admin123'),
            'rol' => 'admin',
        ]);

        $recepcionista = User::create([
            'name' => 'Recepcionista',
            'apellidos' => 'Turno Día',
            'email' => 'recepcion@penglai.com',
            'password' => Hash::make('recepcion123'),
            'rol' => 'recepcionista',
        ]);

        // 2. Catálogo de 12 Habitaciones (Pisos 1, 2 y 3)
        $habitacionesData = [
            // Piso 1 - Estándar ($1,200 MXN, 2 personas)
            ['numero' => '101', 'piso' => 1, 'tipo' => 'Estándar', 'capacidad' => 2, 'precio_noche' => 1200.00, 'estado' => 'disponible'],
            ['numero' => '102', 'piso' => 1, 'tipo' => 'Estándar', 'capacidad' => 2, 'precio_noche' => 1200.00, 'estado' => 'ocupada'],
            ['numero' => '103', 'piso' => 1, 'tipo' => 'Estándar', 'capacidad' => 2, 'precio_noche' => 1200.00, 'estado' => 'disponible'],
            ['numero' => '104', 'piso' => 1, 'tipo' => 'Estándar', 'capacidad' => 2, 'precio_noche' => 1200.00, 'estado' => 'mantenimiento'],

            // Piso 2 - Doble ($1,800 MXN, 4 personas)
            ['numero' => '201', 'piso' => 2, 'tipo' => 'Doble', 'capacidad' => 4, 'precio_noche' => 1800.00, 'estado' => 'reservada'],
            ['numero' => '202', 'piso' => 2, 'tipo' => 'Doble', 'capacidad' => 4, 'precio_noche' => 1800.00, 'estado' => 'disponible'],
            ['numero' => '203', 'piso' => 2, 'tipo' => 'Doble', 'capacidad' => 4, 'precio_noche' => 1800.00, 'estado' => 'limpieza'],
            ['numero' => '204', 'piso' => 2, 'tipo' => 'Doble', 'capacidad' => 4, 'precio_noche' => 1800.00, 'estado' => 'disponible'],

            // Piso 3 - Suite ($3,200 MXN, 4 personas)
            ['numero' => '301', 'piso' => 3, 'tipo' => 'Suite', 'capacidad' => 4, 'precio_noche' => 3200.00, 'estado' => 'ocupada'],
            ['numero' => '302', 'piso' => 3, 'tipo' => 'Suite', 'capacidad' => 4, 'precio_noche' => 3200.00, 'estado' => 'ocupada'],
            ['numero' => '303', 'piso' => 3, 'tipo' => 'Suite', 'capacidad' => 4, 'precio_noche' => 3200.00, 'estado' => 'disponible'],
            ['numero' => '304', 'piso' => 3, 'tipo' => 'Suite', 'capacidad' => 4, 'precio_noche' => 3200.00, 'estado' => 'disponible'],
        ];

        $habitaciones = [];
        foreach ($habitacionesData as $hab) {
            $habitaciones[$hab['numero']] = Habitacion::create($hab);
        }

        // 3. Directorio de Huéspedes
        $g01 = Huesped::create([
            'codigo_huesped' => 'G-01',
            'nombre_completo' => 'Lucía Hernández Ruiz',
            'tipo_documento' => 'INE',
            'documento_identidad' => 'HERL920415MDF',
            'telefono' => '646 555 0101',
            'email' => 'lucia.hernandez@example.com',
        ]);

        $g02 = Huesped::create([
            'codigo_huesped' => 'G-02',
            'nombre_completo' => 'Wei Zhang Chen',
            'tipo_documento' => 'Pasaporte',
            'documento_identidad' => 'E84920145',
            'telefono' => '646 555 0102',
            'email' => 'wei.zhang@example.com',
        ]);

        $g03 = Huesped::create([
            'codigo_huesped' => 'G-03',
            'nombre_completo' => 'Mariana López Soto',
            'tipo_documento' => 'INE',
            'documento_identidad' => 'LOSM950821HBC',
            'telefono' => '646 555 0103',
            'email' => 'mariana.lopez@example.com',
        ]);

        $g04 = Huesped::create([
            'codigo_huesped' => 'G-04',
            'nombre_completo' => 'Daniel Ortega Paz',
            'tipo_documento' => 'Licencia',
            'documento_identidad' => 'BC-8841209',
            'telefono' => '646 555 0104',
            'email' => 'daniel.ortega@example.com',
        ]);

        $g05 = Huesped::create([
            'codigo_huesped' => 'G-05',
            'nombre_completo' => 'Mei Lin Wong',
            'tipo_documento' => 'Pasaporte',
            'documento_identidad' => 'G51903412',
            'telefono' => '646 555 0105',
            'email' => 'mei.wong@example.com',
        ]);

        // 4. Reservaciones iniciales
        $reservaciones = [
            [
                'folio' => 'R-0998',
                'habitacion_id' => $habitaciones['101']->id,
                'huesped_id' => $g02->id,
                'user_id' => $recepcionista->id,
                'fecha_entrada' => '2026-10-02',
                'fecha_salida' => '2026-10-05',
                'numero_huespedes' => 2,
                'total_mxn' => 3600.00,
                'estado' => 'finalizada',
            ],
            [
                'folio' => 'R-1001',
                'habitacion_id' => $habitaciones['102']->id,
                'huesped_id' => $g01->id,
                'user_id' => $recepcionista->id,
                'fecha_entrada' => '2026-11-10',
                'fecha_salida' => '2026-11-14',
                'numero_huespedes' => 2,
                'total_mxn' => 4800.00,
                'estado' => 'en_casa',
            ],
            [
                'folio' => 'R-1002',
                'habitacion_id' => $habitaciones['201']->id,
                'huesped_id' => $g02->id,
                'user_id' => $recepcionista->id,
                'fecha_entrada' => '2026-11-12',
                'fecha_salida' => '2026-11-15',
                'numero_huespedes' => 2,
                'total_mxn' => 5400.00,
                'estado' => 'confirmada',
            ],
            [
                'folio' => 'R-1003',
                'habitacion_id' => $habitaciones['301']->id,
                'huesped_id' => $g03->id,
                'user_id' => $recepcionista->id,
                'fecha_entrada' => '2026-11-09',
                'fecha_salida' => '2026-11-12',
                'numero_huespedes' => 3,
                'total_mxn' => 9600.00,
                'estado' => 'en_casa',
            ],
            [
                'folio' => 'R-1004',
                'habitacion_id' => $habitaciones['202']->id,
                'huesped_id' => $g04->id,
                'user_id' => $admin->id,
                'fecha_entrada' => '2026-11-14',
                'fecha_salida' => '2026-11-18',
                'numero_huespedes' => 4,
                'total_mxn' => 7200.00,
                'estado' => 'confirmada',
            ],
            [
                'folio' => 'R-1005',
                'habitacion_id' => $habitaciones['302']->id,
                'huesped_id' => $g05->id,
                'user_id' => $recepcionista->id,
                'fecha_entrada' => '2026-11-11',
                'fecha_salida' => '2026-11-13',
                'numero_huespedes' => 2,
                'total_mxn' => 6400.00,
                'estado' => 'en_casa',
            ],
        ];

        foreach ($reservaciones as $res) {
            Reservacion::create($res);
        }
    }
}