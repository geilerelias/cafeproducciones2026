<?php

namespace App\Http\Controllers;

use App\Models\EventRequest;
use App\Models\EventRequestActivity;
use App\Models\EventRequestAttachment;
use App\Models\EventRequestStage;
use App\Models\EventRequestTask;
use App\Models\User;
use App\Notifications\EventRequestStageChangedNotification;
use App\Support\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EventRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($this->canAccessModule($user), 403);

        $canManage = $user->canAccess('event.requests.manage');
        $canViewAssigned = $user->canAccess('event.requests.tasks.view-assigned');
        $stages = EventRequestStage::query()->orderBy('sort_order')->get();

        $query = EventRequest::query()
            ->with(['client:id,name,email', 'creator:id,name'])
            ->orderBy('position')
            ->latest('updated_at');

        if ($canManage) {
            // all requests
        } elseif ($user->canAccess('event.requests.view-own')) {
            $query->where('client_user_id', $user->id);
        } elseif ($canViewAssigned) {
            $query->whereHas('tasks', fn ($tasks) => $tasks->where('assigned_to', $user->id));
        } else {
            abort(403);
        }

        return Inertia::render('EventRequests/Index', [
            'stages' => $stages,
            'requests' => $query->get(),
            'canManage' => $canManage,
            'canViewAssigned' => $canViewAssigned && ! $canManage,
            'canCreate' => $canManage || $user->canAccess('event.requests.create'),
            'eventTypes' => self::eventTypes(),
            'identificationTypes' => User::identificationTypesForSelect(),
            'attachmentLabels' => EventRequestAttachment::LABELS,
            'clients' => $canManage
                ? User::role('cliente')->orderBy('name')->get(['id', 'name', 'email'])
                : [],
            'assignees' => $canManage ? self::assignableUsers() : [],
        ]);
    }

    public function show(Request $request, EventRequest $eventRequest): Response
    {
        $this->authorizeRequest($request, $eventRequest);

        $user = $request->user();
        $canManage = $user->canAccess('event.requests.manage');
        $canViewAssigned = $user->canAccess('event.requests.tasks.view-assigned');
        $isClient = $eventRequest->client_user_id === $user->id;

        $eventRequest->load([
            'client:id,name,email,phone',
            'creator:id,name',
            'attachments' => fn ($query) => $canManage
                ? $query->with('uploader:id,name')->latest()
                : $query->where('visible_to_client', true)->with('uploader:id,name')->latest(),
            'tasks' => function ($query) use ($canManage, $canViewAssigned, $user, $isClient) {
                if ($canManage) {
                    $query->orderBy('position');

                    return;
                }

                if ($isClient) {
                    $query->where('visible_to_client', true)->orderBy('position');

                    return;
                }

                if ($canViewAssigned) {
                    $query->where('assigned_to', $user->id)->orderBy('position');
                }
            },
            'tasks.assignee:id,name',
            'activities' => fn ($query) => $canManage
                ? $query->with('user:id,name')->latest()
                : $query->where('visible_to_client', true)->with('user:id,name')->latest(),
        ]);

        return Inertia::render('EventRequests/Show', [
            'eventRequest' => $eventRequest,
            'stages' => EventRequestStage::query()->orderBy('sort_order')->get(),
            'canManage' => $canManage,
            'canViewAssigned' => $canViewAssigned && ! $canManage && ! $isClient,
            'canUploadAttachments' => $canManage || $isClient,
            'eventTypes' => self::eventTypes(),
            'identificationTypes' => User::identificationTypesForSelect(),
            'taskStatuses' => self::taskStatuses(),
            'attachmentLabels' => EventRequestAttachment::LABELS,
            'clients' => $canManage
                ? User::role('cliente')->orderBy('name')->get(['id', 'name', 'email'])
                : [],
            'assignees' => $canManage ? self::assignableUsers() : [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $canManage = $user?->canAccess('event.requests.manage');
        abort_unless($canManage || $user?->canAccess('event.requests.create'), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'event_type' => ['required', 'string', 'max:40'],
            'desired_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'budget_notes' => ['nullable', 'string', 'max:500'],
            'client_user_id' => [$canManage ? 'required' : 'nullable', 'exists:users,id'],
        ]);

        $clientId = $canManage ? (int) $validated['client_user_id'] : $user->id;

        $eventRequest = EventRequest::create([
            'reference' => EventRequest::nextReference(),
            'client_user_id' => $clientId,
            'created_by' => $user->id,
            'title' => $validated['title'],
            'event_type' => $validated['event_type'],
            'desired_date' => $validated['desired_date'] ?? null,
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'guest_count' => $validated['guest_count'] ?? null,
            'budget_notes' => $validated['budget_notes'] ?? null,
            'stage_key' => 'recibida',
            'submitted_at' => now(),
        ]);

        $this->logActivity(
            $eventRequest,
            'created',
            $canManage
                ? 'Solicitud registrada por el equipo CAFE.'
                : 'Solicitud enviada por el cliente.',
        );

        return Feedback::redirectSuccess(
            route('event-requests.show', $eventRequest),
            'Tu solicitud fue registrada. Puedes seguir el avance desde este panel.',
            'Solicitud creada',
        );
    }

    public function storeClient(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('event.requests.manage'), 403);
        $request->merge(['email' => strtolower((string) $request->input('email'))]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            ...User::identificationRules($request),
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')],
        ], User::identificationMessages());

        $client = User::create([
            'name' => $validated['name'],
            'identification_type' => $validated['identification_type'],
            'identification_number' => $validated['identification_number'],
            'phone' => $validated['phone'],
            'email' => $validated['email'],
            'password' => Hash::make(Str::password(16)),
            'email_verified_at' => now(),
        ]);

        Role::findOrCreate('cliente');
        $client->assignRole('cliente');

        return back()
            ->with('success', 'Cliente agregado.')
            ->with('created_client_id', $client->id);
    }

    public function update(Request $request, EventRequest $eventRequest): RedirectResponse
    {
        $this->authorizeRequest($request, $eventRequest);
        $user = $request->user();
        abort_unless($user->canAccess('event.requests.manage'), 403);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:200'],
            'event_type' => ['sometimes', 'required', 'string', 'max:40'],
            'desired_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'budget_notes' => ['nullable', 'string', 'max:500'],
            'client_user_id' => ['sometimes', 'required', 'exists:users,id'],
            'client_message' => ['nullable', 'string', 'max:5000'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
            'stage_key' => ['sometimes', 'required', 'string', 'exists:event_request_stages,key'],
        ]);

        $previousStage = $eventRequest->stage_key;

        $eventRequest->fill($validated);

        if (isset($validated['stage_key']) && $validated['stage_key'] !== $previousStage) {
            $this->applyStageTransition($eventRequest, $previousStage, $validated['stage_key']);
        }

        $eventRequest->save();

        return Feedback::success('Los cambios de la solicitud fueron guardados.', 'Solicitud actualizada');
    }

    public function updateStage(Request $request, EventRequest $eventRequest): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->canAccess('event.requests.manage'), 403);

        $validated = $request->validate([
            'stage_key' => ['required', 'string', 'exists:event_request_stages,key'],
            'position' => ['nullable', 'integer', 'min:0'],
        ]);

        $previousStage = $eventRequest->stage_key;
        $eventRequest->stage_key = $validated['stage_key'];
        $eventRequest->position = $validated['position'] ?? 0;

        if ($previousStage !== $validated['stage_key']) {
            $this->applyStageTransition($eventRequest, $previousStage, $validated['stage_key']);
        }

        $eventRequest->save();

        return Feedback::success('La solicitud se movio a la nueva etapa.', 'Etapa actualizada');
    }

    public function storeTask(Request $request, EventRequest $eventRequest): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('event.requests.manage'), 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', 'string', 'in:pendiente,en_progreso,completada,bloqueada'],
            'visible_to_client' => ['boolean'],
            'due_date' => ['nullable', 'date'],
            'assigned_to' => ['nullable', 'exists:users,id'],
        ]);

        $task = $eventRequest->tasks()->create([
            ...$validated,
            'visible_to_client' => $validated['visible_to_client'] ?? true,
            'position' => (int) $eventRequest->tasks()->max('position') + 1,
            'completed_at' => ($validated['status'] ?? '') === 'completada' ? now() : null,
        ]);

        $this->logActivity(
            $eventRequest,
            'task_created',
            'Nueva tarea: '.$task->title,
            ['task_id' => $task->id],
            $task->visible_to_client,
        );

        return Feedback::success('La tarea quedo registrada en el tablero.', 'Tarea agregada');
    }

    public function updateTask(Request $request, EventRequest $eventRequest, EventRequestTask $task): RedirectResponse
    {
        abort_unless($task->event_request_id === $eventRequest->id, 404);

        $user = $request->user();
        $canManage = $user?->canAccess('event.requests.manage');
        $canUpdateAssigned = $user?->canAccess('event.requests.tasks.view-assigned')
            && $task->assigned_to === $user->id;

        abort_unless($canManage || $canUpdateAssigned, 403);

        if ($canManage) {
            $validated = $request->validate([
                'title' => ['sometimes', 'required', 'string', 'max:200'],
                'description' => ['nullable', 'string', 'max:2000'],
                'status' => ['sometimes', 'required', 'string', 'in:pendiente,en_progreso,completada,bloqueada'],
                'visible_to_client' => ['boolean'],
                'due_date' => ['nullable', 'date'],
                'assigned_to' => ['nullable', 'exists:users,id'],
            ]);
        } else {
            $validated = $request->validate([
                'status' => ['required', 'string', 'in:pendiente,en_progreso,completada,bloqueada'],
            ]);
        }

        $wasCompleted = $task->status === 'completada';

        $task->fill($validated);

        if (isset($validated['status'])) {
            $task->completed_at = $validated['status'] === 'completada' ? now() : null;

            if (! $wasCompleted && $validated['status'] === 'completada' && $task->visible_to_client) {
                $this->logActivity(
                    $eventRequest,
                    'task_completed',
                    'Tarea completada: '.$task->title,
                    ['task_id' => $task->id],
                );
            }
        }

        $task->save();

        return Feedback::success('El estado de la tarea fue actualizado.', 'Tarea actualizada');
    }

    public function storeComment(Request $request, EventRequest $eventRequest): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('event.requests.manage'), 403);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
            'visible_to_client' => ['boolean'],
        ]);

        $visible = $validated['visible_to_client'] ?? true;

        $this->logActivity($eventRequest, 'comment', $validated['body'], [], $visible);

        if ($visible) {
            $eventRequest->update([
                'client_message' => $validated['body'],
            ]);
        }

        return Feedback::success('La actualizacion ya es visible en el seguimiento.', 'Actualizacion publicada');
    }

    public function storeAttachment(Request $request, EventRequest $eventRequest): RedirectResponse
    {
        $this->authorizeRequest($request, $eventRequest);

        $user = $request->user();
        $canManage = $user->canAccess('event.requests.manage');
        $isClient = $eventRequest->client_user_id === $user->id;
        abort_unless($canManage || $isClient, 403);

        $validated = $request->validate([
            'file' => ['required', 'file', 'max:10240', 'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,zip'],
            'label' => ['required', 'string', 'in:'.implode(',', array_keys(EventRequestAttachment::LABELS))],
            'visible_to_client' => ['boolean'],
        ]);

        $file = $validated['file'];
        $path = $file->store('event-requests/'.$eventRequest->id, 'local');

        $eventRequest->attachments()->create([
            'uploaded_by' => $user->id,
            'label' => $validated['label'],
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
            'visible_to_client' => $canManage ? ($validated['visible_to_client'] ?? true) : true,
        ]);

        $this->logActivity(
            $eventRequest,
            'attachment_uploaded',
            'Se adjunto: '.(EventRequestAttachment::LABELS[$validated['label']] ?? $validated['label']),
            [],
            $canManage ? ($validated['visible_to_client'] ?? true) : true,
        );

        return Feedback::success('El archivo se subio correctamente.', 'Adjunto guardado');
    }

    public function downloadAttachment(Request $request, EventRequest $eventRequest, EventRequestAttachment $attachment): StreamedResponse
    {
        abort_unless($attachment->event_request_id === $eventRequest->id, 404);
        $this->authorizeRequest($request, $eventRequest);

        $user = $request->user();
        abort_unless(
            $user->canAccess('event.requests.manage')
            || ($eventRequest->client_user_id === $user->id && $attachment->visible_to_client)
            || ($attachment->visible_to_client && $user->canAccess('event.requests.tasks.view-assigned')
                && $eventRequest->tasks()->where('assigned_to', $user->id)->exists()),
            403
        );

        abort_unless(Storage::disk('local')->exists($attachment->path), 404);

        return Storage::disk('local')->download($attachment->path, $attachment->original_name);
    }

    public function destroyAttachment(Request $request, EventRequest $eventRequest, EventRequestAttachment $attachment): RedirectResponse
    {
        abort_unless($attachment->event_request_id === $eventRequest->id, 404);
        $this->authorizeRequest($request, $eventRequest);

        $user = $request->user();
        $canManage = $user->canAccess('event.requests.manage');
        abort_unless($canManage || ($attachment->uploaded_by === $user->id && $eventRequest->client_user_id === $user->id), 403);

        $attachment->deleteFile();
        $attachment->delete();

        return Feedback::success('El archivo fue eliminado.', 'Adjunto eliminado');
    }

    private function canAccessModule(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->canAccess('event.requests.create')
            || $user->canAccess('event.requests.view-own')
            || $user->canAccess('event.requests.manage')
            || $user->canAccess('event.requests.tasks.view-assigned');
    }

    private function authorizeRequest(Request $request, EventRequest $eventRequest): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($user->canAccess('event.requests.manage')) {
            return;
        }

        if ($user->canAccess('event.requests.view-own') && $eventRequest->client_user_id === $user->id) {
            return;
        }

        if ($user->canAccess('event.requests.tasks.view-assigned')
            && $eventRequest->tasks()->where('assigned_to', $user->id)->exists()) {
            return;
        }

        abort(403);
    }

    private function applyStageTransition(EventRequest $eventRequest, string $from, string $to): void
    {
        $stages = EventRequestStage::query()->whereIn('key', [$from, $to])->get()->keyBy('key');
        $fromName = $stages[$from]->name ?? $from;
        $toName = $stages[$to]->name ?? $to;

        $terminal = $stages[$to]->is_terminal ?? false;
        $eventRequest->completed_at = $terminal ? now() : null;

        $this->logActivity(
            $eventRequest,
            'stage_change',
            "La solicitud paso de «{$fromName}» a «{$toName}».",
            ['from' => $from, 'to' => $to],
        );

        $eventRequest->loadMissing('client');

        if ($eventRequest->client) {
            $eventRequest->client->notify(
                new EventRequestStageChangedNotification($eventRequest, $fromName, $toName)
            );
        }
    }

    private function logActivity(
        EventRequest $eventRequest,
        string $type,
        string $body,
        array $meta = [],
        bool $visible = true,
    ): void {
        EventRequestActivity::create([
            'event_request_id' => $eventRequest->id,
            'user_id' => auth()->id(),
            'type' => $type,
            'body' => $body,
            'meta' => $meta ?: null,
            'visible_to_client' => $visible,
        ]);
    }

    /** @return \Illuminate\Support\Collection<int, User> */
    private static function assignableUsers()
    {
        return User::query()
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['trabajador', 'admin', 'superadmin']))
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }

    /** @return array<string, string> */
    private static function eventTypes(): array
    {
        return [
            'corporativo' => 'Corporativo',
            'social' => 'Social / familiar',
            'institucional' => 'Institucional',
            'feria' => 'Feria / stand',
            'otro' => 'Otro',
        ];
    }

    /** @return array<string, string> */
    private static function taskStatuses(): array
    {
        return [
            'pendiente' => 'Pendiente',
            'en_progreso' => 'En progreso',
            'completada' => 'Completada',
            'bloqueada' => 'Bloqueada',
        ];
    }
}
