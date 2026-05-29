<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventRequestStageSeeder extends Seeder
{
    public function run(): void
    {
        $stages = [
            ['key' => 'recibida', 'name' => 'Recibida', 'description' => 'Solicitud enviada y en espera de revision.', 'color' => '#6b625d', 'sort_order' => 1, 'is_terminal' => false],
            ['key' => 'en_revision', 'name' => 'En revision', 'description' => 'El equipo evalua alcance, fecha y viabilidad.', 'color' => '#b45309', 'sort_order' => 2, 'is_terminal' => false],
            ['key' => 'aceptada', 'name' => 'Aceptada', 'description' => 'La solicitud fue aprobada. Inicia la planeacion.', 'color' => '#15803d', 'sort_order' => 3, 'is_terminal' => false],
            ['key' => 'en_produccion', 'name' => 'En produccion', 'description' => 'Montaje, logistica y ejecucion del evento.', 'color' => '#a8322b', 'sort_order' => 4, 'is_terminal' => false],
            ['key' => 'lista', 'name' => 'Lista / Entregada', 'description' => 'Evento finalizado y cerrado.', 'color' => '#1d4ed8', 'sort_order' => 5, 'is_terminal' => true],
            ['key' => 'rechazada', 'name' => 'Rechazada', 'description' => 'No procede en las condiciones actuales.', 'color' => '#71717a', 'sort_order' => 6, 'is_terminal' => true],
        ];

        $now = now();

        foreach ($stages as $stage) {
            DB::table('event_request_stages')->updateOrInsert(
                ['key' => $stage['key']],
                [...$stage, 'visible_to_client' => true, 'created_at' => $now, 'updated_at' => $now],
            );
        }
    }
}
