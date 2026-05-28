<?php

namespace App\Http\Controllers;

use App\Models\EmployeeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeRequestController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user?->canAccess('employee.requests.create') || $user?->canAccess('employee.requests.manage'), 403);

        $query = EmployeeRequest::query()->with('user:id,name,email')->latest();

        if (! $user->canAccess('employee.requests.manage')) {
            $query->where('user_id', $user->id);
        }

        return Inertia::render('EmployeeRequests/Index', [
            'requests' => $query->get(),
            'requestTypes' => [
                'vale' => 'Vale',
                'estado_cuenta' => 'Estado de cuenta',
                'desprendible' => 'Desprendible',
                'certificado_laboral' => 'Certificado laboral',
                'otro' => 'Otro',
            ],
            'canManage' => $user->canAccess('employee.requests.manage'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->canAccess('employee.requests.create'), 403);

        $validated = $request->validate([
            'type' => ['required', 'string', 'max:80'],
            'details' => ['nullable', 'string', 'max:2000'],
        ]);

        EmployeeRequest::create([
            ...$validated,
            'user_id' => $user->id,
        ]);

        return back()->with('success', 'Solicitud enviada.');
    }

    public function update(Request $request, EmployeeRequest $employeeRequest): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->canAccess('employee.requests.manage'), 403);

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pendiente,en_revision,aprobado,rechazado,entregado'],
            'admin_response' => ['nullable', 'string', 'max:2000'],
        ]);

        $employeeRequest->update([
            ...$validated,
            'reviewed_by' => $user->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Solicitud actualizada.');
    }
}
