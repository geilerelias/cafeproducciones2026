<?php

use App\Models\EventRequest;
use App\Models\EventRequestTask;
use App\Models\User;
use App\Notifications\EventRequestStageChangedNotification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;

function grantEventRequestPermissions(User $user, array $permissions): void
{
    foreach ($permissions as $permission) {
        Permission::findOrCreate($permission);
        $user->givePermissionTo($permission);
    }
}

test('client can create and view own event request', function () {
    $client = User::factory()->create();
    grantEventRequestPermissions($client, ['event.requests.create', 'event.requests.view-own']);

    $response = $this->actingAs($client)->post('/event-requests', [
        'title' => 'Lanzamiento producto',
        'event_type' => 'corporativo',
        'desired_date' => '2026-09-15',
        'location' => 'Bogota',
        'description' => 'Evento para 200 personas',
        'guest_count' => 200,
    ]);

    $eventRequest = EventRequest::query()->first();

    expect($eventRequest)->not->toBeNull();
    expect($eventRequest->client_user_id)->toBe($client->id);
    expect($eventRequest->stage_key)->toBe('recibida');

    $response->assertRedirect(route('event-requests.show', $eventRequest, absolute: false));

    $this->actingAs($client)
        ->get(route('event-requests.show', $eventRequest, absolute: false))
        ->assertOk();
});

test('client cannot view another clients event request', function () {
    $client = User::factory()->create();
    $other = User::factory()->create();
    grantEventRequestPermissions($client, ['event.requests.view-own']);

    $eventRequest = EventRequest::factory()->create([
        'client_user_id' => $other->id,
        'created_by' => $other->id,
    ]);

    $this->actingAs($client)
        ->get(route('event-requests.show', $eventRequest, absolute: false))
        ->assertForbidden();
});

test('admin can manage stages and tasks and notifies client', function () {
    Notification::fake();

    $admin = User::factory()->create();
    $client = User::factory()->create();
    grantEventRequestPermissions($admin, ['event.requests.manage']);

    $eventRequest = EventRequest::factory()->create([
        'client_user_id' => $client->id,
        'created_by' => $admin->id,
        'stage_key' => 'recibida',
    ]);

    $this->actingAs($admin)
        ->patch(route('event-requests.stage.update', $eventRequest, absolute: false), [
            'stage_key' => 'aceptada',
            'position' => 0,
        ])
        ->assertRedirect();

    expect($eventRequest->fresh()->stage_key)->toBe('aceptada');

    Notification::assertSentTo($client, EventRequestStageChangedNotification::class);

    $this->actingAs($admin)
        ->post(route('event-requests.tasks.store', $eventRequest, absolute: false), [
            'title' => 'Confirmar locacion',
            'status' => 'pendiente',
            'visible_to_client' => true,
        ])
        ->assertRedirect();

    expect($eventRequest->tasks()->count())->toBe(1);
});

test('worker only sees and updates assigned tasks', function () {
    $worker = User::factory()->create();
    $otherWorker = User::factory()->create();
    $client = User::factory()->create();
    grantEventRequestPermissions($worker, ['event.requests.tasks.view-assigned']);

    $eventRequest = EventRequest::factory()->create([
        'client_user_id' => $client->id,
        'created_by' => $client->id,
    ]);

    $assignedTask = EventRequestTask::query()->create([
        'event_request_id' => $eventRequest->id,
        'title' => 'Montaje escenario',
        'status' => 'pendiente',
        'assigned_to' => $worker->id,
        'visible_to_client' => false,
    ]);

    EventRequestTask::query()->create([
        'event_request_id' => $eventRequest->id,
        'title' => 'Otra tarea',
        'status' => 'pendiente',
        'assigned_to' => $otherWorker->id,
    ]);

    $this->actingAs($worker)->get(route('event-requests.index', absolute: false))->assertOk();

    $this->actingAs($worker)
        ->get(route('event-requests.show', $eventRequest, absolute: false))
        ->assertOk();

    $this->actingAs($worker)
        ->patch(route('event-requests.tasks.update', [$eventRequest, $assignedTask], absolute: false), [
            'status' => 'en_progreso',
        ])
        ->assertRedirect();

    expect($assignedTask->fresh()->status)->toBe('en_progreso');
});

test('client can upload attachment to own event request', function () {
    Storage::fake('local');

    $client = User::factory()->create();
    grantEventRequestPermissions($client, ['event.requests.create', 'event.requests.view-own']);

    $eventRequest = EventRequest::factory()->create([
        'client_user_id' => $client->id,
        'created_by' => $client->id,
    ]);

    $file = UploadedFile::fake()->create('brief.pdf', 120, 'application/pdf');

    $this->actingAs($client)
        ->post(route('event-requests.attachments.store', $eventRequest, absolute: false), [
            'file' => $file,
            'label' => 'brief',
        ])
        ->assertRedirect();

    expect($eventRequest->attachments()->count())->toBe(1);
});

test('admin can manage event request stages configuration', function () {
    $admin = User::factory()->create();
    grantEventRequestPermissions($admin, ['event.requests.manage']);

    $this->actingAs($admin)
        ->get(route('event-request-stages.index', absolute: false))
        ->assertOk();

    $this->actingAs($admin)
        ->post(route('event-request-stages.store', absolute: false), [
            'name' => 'Postproduccion',
            'color' => '#2563eb',
            'sort_order' => 10,
        ])
        ->assertRedirect();

    expect(\App\Models\EventRequestStage::query()->where('name', 'Postproduccion')->exists())->toBeTrue();
});
