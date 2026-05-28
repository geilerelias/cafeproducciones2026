<?php

namespace App\Http\Controllers;

use App\Models\EmployeeAppointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeAppointmentController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user?->canAccess('appointments.create') || $user?->canAccess('appointments.manage'), 403);

        $query = EmployeeAppointment::query()->with('user:id,name,email')->latest('scheduled_at');

        if (! $user->canAccess('appointments.manage')) {
            $query->where('user_id', $user->id);
        }

        return Inertia::render('Appointments/Index', [
            'appointments' => $query->get(),
            'canManage' => $user->canAccess('appointments.manage'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->canAccess('appointments.create'), 403);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:160'],
            'scheduled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        EmployeeAppointment::create([
            ...$validated,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Cita creada.');
    }

    public function update(Request $request, EmployeeAppointment $employeeAppointment): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('appointments.manage'), 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pendiente,confirmada,cancelada,atendida'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $employeeAppointment->update($validated);

        return back()->with('success', 'Cita actualizada.');
    }
}
