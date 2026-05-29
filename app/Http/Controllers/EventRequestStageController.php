<?php

namespace App\Http\Controllers;

use App\Models\EventRequest;
use App\Models\EventRequestStage;
use App\Support\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class EventRequestStageController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->canAccess('event.requests.manage'), 403);

        return Inertia::render('EventRequests/Stages', [
            'stages' => EventRequestStage::query()
                ->orderBy('sort_order')
                ->get()
                ->map(fn (EventRequestStage $stage) => [
                    ...$stage->toArray(),
                    'requests_count' => EventRequest::query()->where('stage_key', $stage->key)->count(),
                ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('event.requests.manage'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', 'string', 'max:20'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_terminal' => ['boolean'],
            'visible_to_client' => ['boolean'],
        ]);

        $key = $this->uniqueKey(Str::slug($validated['name'], '_'));

        EventRequestStage::create([
            'key' => $key,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'],
            'sort_order' => $validated['sort_order'] ?? ((int) EventRequestStage::query()->max('sort_order')) + 1,
            'is_terminal' => $validated['is_terminal'] ?? false,
            'visible_to_client' => $validated['visible_to_client'] ?? true,
        ]);

        return Feedback::success('Etapa creada correctamente.', 'Etapa agregada');
    }

    public function update(Request $request, EventRequestStage $eventRequestStage): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('event.requests.manage'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'color' => ['required', 'string', 'max:20'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'is_terminal' => ['boolean'],
            'visible_to_client' => ['boolean'],
        ]);

        $eventRequestStage->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'],
            'sort_order' => $validated['sort_order'],
            'is_terminal' => $validated['is_terminal'] ?? false,
            'visible_to_client' => $validated['visible_to_client'] ?? true,
        ]);

        return Feedback::success('Cambios guardados.', 'Etapa actualizada');
    }

    public function destroy(Request $request, EventRequestStage $eventRequestStage): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('event.requests.manage'), 403);

        $inUse = EventRequest::query()->where('stage_key', $eventRequestStage->key)->exists();

        if ($inUse) {
            return Feedback::error('Hay solicitudes en esta etapa. Muevelas antes de eliminarla.', 'No se puede eliminar');
        }

        $eventRequestStage->delete();

        return Feedback::success('Etapa eliminada.', 'Eliminada');
    }

    private function uniqueKey(string $base): string
    {
        $key = $base ?: 'etapa';
        $suffix = 1;

        while (EventRequestStage::query()->where('key', $key)->exists()) {
            $key = $base.'_'.$suffix;
            $suffix++;
        }

        return $key;
    }
}
