<?php

namespace App\Http\Controllers;

use App\Models\CompanyEvent;
use App\Models\EventAssignment;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CompanyEventController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user?->canAccess('events.view') || $user?->canAccess('events.manage'), 403);

        $events = CompanyEvent::query()
            ->with([
                'assignments.user:id,name,email',
                'toolAssignments.tool:id,name,code',
                'toolAssignments.user:id,name,email',
            ])
            ->withCount([
                'assignments',
                'assignments as registered_count' => fn ($query) => $query->whereNotNull('registered_at'),
            ])
            ->latest('starts_at')
            ->get();

        if (! $user->canAccess('events.manage')) {
            $events = $events
                ->filter(fn (CompanyEvent $event) => $event->assignments->contains('user_id', $user->id))
                ->map(function (CompanyEvent $event) use ($user) {
                    $event->setRelation('assignments', $event->assignments->where('user_id', $user->id)->values());
                    $event->setRelation('toolAssignments', $event->toolAssignments->where('user_id', $user->id)->values());

                    return $event;
                })
                ->values();
        }

        return Inertia::render('Events/Index', [
            'events' => $events,
            'employees' => User::role('trabajador')->orderBy('name')->get(['id', 'name', 'email']),
            'tools' => Tool::query()->orderBy('name')->get(['id', 'name', 'code', 'status']),
            'canManage' => $user->canAccess('events.manage'),
            'canRegister' => $user->canAccess('events.register'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('events.manage'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:180'],
            'location' => ['nullable', 'string', 'max:180'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        CompanyEvent::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Evento creado.');
    }

    public function assignEmployee(Request $request, CompanyEvent $companyEvent): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('events.manage'), 403);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'task' => ['required', 'string', 'max:180'],
            'payment_amount' => ['required', 'numeric', 'min:0'],
            'payment_status' => ['required', 'string', 'in:pendiente,aprobado,pagado'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $employee = User::findOrFail($validated['user_id']);
        abort_unless($employee->hasRole('trabajador'), 403);

        EventAssignment::updateOrCreate([
            'company_event_id' => $companyEvent->id,
            'user_id' => $validated['user_id'],
        ], $validated);

        return back()->with('success', 'Empleado asignado al evento.');
    }

    public function register(Request $request, CompanyEvent $companyEvent): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->canAccess('events.register'), 403);

        $assignment = $companyEvent->assignments()->where('user_id', $user->id)->firstOrFail();
        $assignment->update(['registered_at' => now()]);

        return back()->with('success', 'Registro de participacion confirmado.');
    }

    public function assignTool(Request $request, CompanyEvent $companyEvent): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('tools.manage'), 403);

        $validated = $request->validate([
            'tool_id' => ['required', 'exists:tools,id'],
            'user_id' => ['required', 'exists:users,id'],
            'condition_out' => ['nullable', 'string', 'max:180'],
            'responsibility_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $companyEvent->toolAssignments()->create([
            ...$validated,
            'assigned_at' => now(),
        ]);

        Tool::whereKey($validated['tool_id'])->update(['status' => 'asignada']);

        return back()->with('success', 'Herramienta asignada.');
    }
}
