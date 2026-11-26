<?php

namespace App\Console\Commands;

use App\Models\Habitacion;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Mide cuánto tardan las pantallas principales con muchos datos.
 *
 * Uso (en una base de datos aparte para no borrar los datos reales):
 *   DB_DATABASE=hotel_manager_rendimiento php artisan medir:rendimiento
 */
class MedirRendimiento extends Command
{
    protected $signature = 'medir:rendimiento {--reservaciones=5000} {--huespedes=1000} {--repeticiones=5}';

    protected $description = 'Carga muchos datos de prueba y mide tiempo y consultas de cada pantalla';

    public function handle(): int
    {
        // Protección: este comando borra la base de datos, así que solo corre en la de pruebas
        $baseDatos = DB::connection()->getDatabaseName();
        if (! str_ends_with($baseDatos, '_rendimiento')) {
            $this->error("Este comando borra la base de datos. Úsalo con DB_DATABASE=hotel_manager_rendimiento (actual: {$baseDatos}).");
            return self::FAILURE;
        }

        $this->info('1) Preparando base de datos con datos de prueba...');
        $this->call('migrate:fresh', ['--seed' => true, '--force' => true]);
        $this->cargarDatos((int) $this->option('huespedes'), (int) $this->option('reservaciones'));

        $this->info('2) Midiendo pantallas...');
        Auth::login(User::where('rol', 'admin')->first());

        $paginas = [
            'Inicio' => '/',
            'Reservaciones' => '/reservaciones',
            'Huéspedes' => '/huespedes',
            'Disponibilidad (consulta)' => '/disponibilidad?consultar=1&fecha_entrada=2027-03-01&fecha_salida=2027-03-04&capacidad=2&tipo=Todas',
        ];

        $filas = [];
        foreach ($paginas as $nombre => $url) {
            [$ms, $consultas, $kb] = $this->medir($url, (int) $this->option('repeticiones'));
            $filas[] = [$nombre, $ms, $consultas, $kb];
        }

        $this->table(['Pantalla', 'Tiempo promedio (ms)', 'Consultas SQL', 'Tamaño HTML (KB)'], $filas);
        $this->line('Datos: ' . DB::table('reservaciones')->count() . ' reservaciones, ' . DB::table('huespedes')->count() . ' huéspedes.');

        return self::SUCCESS;
    }

    /**
     * Abre la página varias veces y regresa: tiempo promedio, número de consultas y tamaño.
     */
    private function medir(string $url, int $repeticiones): array
    {
        $kernel = app(Kernel::class);
        $tiempos = [];
        $consultas = 0;
        $tamano = 0;

        for ($i = 0; $i < $repeticiones; $i++) {
            DB::flushQueryLog();
            DB::enableQueryLog();

            $inicio = microtime(true);
            $respuesta = $kernel->handle(Request::create($url, 'GET'));
            $tiempos[] = (microtime(true) - $inicio) * 1000;

            $consultas = count(DB::getQueryLog());
            $tamano = strlen($respuesta->getContent());
            DB::disableQueryLog();
        }

        return [round(array_sum($tiempos) / count($tiempos), 1), $consultas, round($tamano / 1024)];
    }

    /**
     * Inserta huéspedes y reservaciones de prueba. Siempre genera los mismos datos
     * (mt_srand fijo) para que la medición de antes y después sea comparable.
     */
    private function cargarDatos(int $totalHuespedes, int $totalReservaciones): void
    {
        mt_srand(2026);
        $ahora = now();

        $huespedes = [];
        for ($i = 1; $i <= $totalHuespedes; $i++) {
            $huespedes[] = [
                'codigo_huesped' => 'P-' . $i,
                'nombre_completo' => "Huésped Prueba {$i}",
                'tipo_documento' => 'INE',
                'documento_identidad' => 'DOC' . $i,
                'telefono' => '646 555 ' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'email' => "huesped{$i}@example.com",
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }
        foreach (array_chunk($huespedes, 500) as $bloque) {
            DB::table('huespedes')->insert($bloque);
        }

        $habitaciones = Habitacion::pluck('id')->all();
        $idsHuespedes = DB::table('huespedes')->pluck('id')->all();
        $usuario = User::first()->id;

        $reservaciones = [];
        for ($i = 1; $i <= $totalReservaciones; $i++) {
            // Reservaciones pasadas entre 2024 y 2026, la mayoría ya terminadas
            $entrada = now()->subDays(mt_rand(30, 900));
            $noches = mt_rand(1, 5);
            $reservaciones[] = [
                'folio' => 'P-' . $i,
                'habitacion_id' => $habitaciones[array_rand($habitaciones)],
                'huesped_id' => $idsHuespedes[array_rand($idsHuespedes)],
                'user_id' => $usuario,
                'fecha_entrada' => $entrada->format('Y-m-d'),
                'fecha_salida' => $entrada->copy()->addDays($noches)->format('Y-m-d'),
                'numero_huespedes' => 2,
                'total_mxn' => 1200 * $noches,
                'estado' => mt_rand(1, 10) === 1 ? 'cancelada' : 'finalizada',
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ];
        }
        foreach (array_chunk($reservaciones, 500) as $bloque) {
            DB::table('reservaciones')->insert($bloque);
        }
    }
}
