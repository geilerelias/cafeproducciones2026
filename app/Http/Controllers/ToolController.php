<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ToolController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->canAccess('tools.manage'), 403);

        return Inertia::render('Tools/Index', [
            'tools' => Tool::query()
                ->with(['assignments.user:id,name,email', 'assignments.event:id,name'])
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('tools.manage'), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'code' => ['nullable', 'string', 'max:80', 'unique:tools,code'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Tool::create($validated);

        return back()->with('success', 'Herramienta creada.');
    }
}
