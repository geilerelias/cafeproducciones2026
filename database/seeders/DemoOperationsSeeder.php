<?php

namespace Database\Seeders;

use App\Models\CustomForm;
use App\Models\CompanyEvent;
use App\Models\EventAssignment;
use App\Models\EmployeeRequest;
use App\Models\News;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoOperationsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@cafeproducciones.test')->first();
        $worker = User::query()->where('email', 'trabajador@cafeproducciones.test')->first();

        if ($admin) {
            CustomForm::query()->firstOrCreate([
                'title' => 'Registro de personal para eventos',
            ], [
                'created_by' => $admin->id,
                'description' => 'Datos basicos para seleccionar personal logistico y tecnico.',
                'audience' => 'trabajador',
                'is_active' => true,
                'fields' => [
                    ['label' => 'Nombre completo', 'key' => 'nombre', 'type' => 'text', 'required' => true, 'options' => []],
                    ['label' => 'Cedula', 'key' => 'cedula', 'type' => 'text', 'required' => true, 'options' => []],
                    ['label' => 'Edad', 'key' => 'edad', 'type' => 'number', 'required' => true, 'options' => []],
                    ['label' => 'Talla de camisa', 'key' => 'talla_camisa', 'type' => 'select', 'required' => false, 'options' => ['S', 'M', 'L', 'XL']],
                    ['label' => 'Tipo de sangre', 'key' => 'tipo_sangre', 'type' => 'text', 'required' => false, 'options' => []],
                    ['label' => 'EPS', 'key' => 'eps', 'type' => 'text', 'required' => false, 'options' => []],
                ],
            ]);

            CustomForm::query()->firstOrCreate([
                'title' => 'Encuesta de satisfaccion de cliente',
            ], [
                'created_by' => $admin->id,
                'description' => 'Medicion posterior a eventos y servicios.',
                'audience' => 'cliente',
                'is_active' => true,
                'fields' => [
                    ['label' => 'Calificacion general', 'key' => 'calificacion', 'type' => 'number', 'required' => true, 'options' => []],
                    ['label' => 'Servicio contratado', 'key' => 'servicio', 'type' => 'text', 'required' => true, 'options' => []],
                    ['label' => 'Comentarios', 'key' => 'comentarios', 'type' => 'textarea', 'required' => false, 'options' => []],
                ],
            ]);

            $seedNews = [
                [
                    'title' => 'Joven elige Joven',
                    'category' => 'Noticias',
                    'platform' => 'Instagram',
                    'source_url' => 'https://www.instagram.com/p/CVfwRpgJKfy/',
                    'excerpt' => 'Produccion integral para elecciones de consejos de juventud con equipo logistico y tecnico.',
                ],
                [
                    'title' => 'Jornada de actualizacion con OIM',
                    'category' => 'Noticias',
                    'platform' => 'Instagram',
                    'source_url' => 'https://www.instagram.com/p/CVgTakLPD3v/',
                    'excerpt' => 'Acompanamiento logistico, soporte tecnico, alimentacion y apoyo operativo para jornada institucional.',
                ],
                [
                    'title' => 'Guerra de DJ',
                    'category' => 'Noticias',
                    'platform' => 'Facebook',
                    'source_url' => 'https://www.facebook.com/CAFE-Producciones-101286955178495',
                    'excerpt' => 'Produccion y adecuacion de escenarios para una experiencia en vivo de alto impacto.',
                ],
            ];

            foreach ($seedNews as $item) {
                News::query()->firstOrCreate([
                    'slug' => Str::slug($item['title']),
                ], [
                    ...$item,
                    'created_by' => $admin->id,
                    'body' => $item['excerpt'],
                    'status' => 'published',
                    'is_featured' => true,
                    'published_at' => now()->subDays(3),
                    'meta_title' => $item['title'],
                    'meta_description' => $item['excerpt'],
                ]);
            }
        }

        if ($worker) {
            EmployeeRequest::query()->firstOrCreate([
                'user_id' => $worker->id,
                'type' => 'vale',
                'status' => 'pendiente',
            ], [
                'details' => 'Solicitud demo de vale para gastos de transporte.',
            ]);

            EmployeeRequest::query()->firstOrCreate([
                'user_id' => $worker->id,
                'type' => 'desprendible',
                'status' => 'en_revision',
            ], [
                'details' => 'Solicitud demo de desprendible del ultimo pago.',
            ]);
        }

        if ($admin && $worker) {
            $event = CompanyEvent::query()->firstOrCreate([
                'name' => 'Montaje demo evento corporativo',
            ], [
                'created_by' => $admin->id,
                'location' => 'Riohacha',
                'starts_at' => now()->addDays(7)->setTime(8, 0),
                'ends_at' => now()->addDays(7)->setTime(18, 0),
                'status' => 'planeado',
                'description' => 'Evento demo para validar asignacion de tareas, pagos y herramientas.',
            ]);

            EventAssignment::query()->updateOrCreate([
                'company_event_id' => $event->id,
                'user_id' => $worker->id,
            ], [
                'task' => 'Apoyo en montaje de sonido',
                'payment_amount' => 180000,
                'payment_status' => 'pendiente',
                'notes' => 'Debe llegar una hora antes del montaje.',
            ]);

            $tool = Tool::query()->firstOrCreate([
                'code' => 'MIC-001',
            ], [
                'name' => 'Microfono inalambrico',
                'status' => 'disponible',
                'notes' => 'Herramienta demo.',
            ]);

            $event->toolAssignments()->firstOrCreate([
                'tool_id' => $tool->id,
                'user_id' => $worker->id,
            ], [
                'assigned_at' => now(),
                'condition_out' => 'Bueno',
                'status' => 'asignada',
                'responsibility_notes' => 'El trabajador responde por entrega en buen estado.',
            ]);

            $tool->update(['status' => 'asignada']);
        }
    }
}
