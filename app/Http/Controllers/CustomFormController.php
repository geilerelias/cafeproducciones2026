<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CustomFormController extends Controller
{
    public function index(Request $request): Response
    {
        abort_unless($request->user()?->canAccess('forms.manage'), 403);

        return Inertia::render('AdminForms/Index', [
            'forms' => CustomForm::query()
                ->withCount('submissions')
                ->with('creator:id,name,email')
                ->latest()
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('forms.manage'), 403);

        $validated = $this->validatedForm($request);

        CustomForm::create([
            ...$validated,
            'slug' => $this->uniqueSlug(($validated['slug'] ?? null) ?: $validated['title']),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Formulario creado.');
    }

    public function update(Request $request, CustomForm $customForm): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('forms.manage'), 403);

        $validated = $this->validatedForm($request, $customForm);

        $customForm->update([
            ...$validated,
            'slug' => $this->uniqueSlug(($validated['slug'] ?? null) ?: $validated['title'], $customForm->id),
        ]);

        return back()->with('success', 'Formulario actualizado.');
    }

    public function destroy(Request $request, CustomForm $customForm): RedirectResponse
    {
        abort_unless($request->user()?->canAccess('forms.manage'), 403);

        $customForm->delete();

        return back()->with('success', 'Formulario eliminado.');
    }

    private function validatedForm(Request $request, ?CustomForm $customForm = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('custom_forms', 'slug')->ignore($customForm)],
            'description' => ['nullable', 'string', 'max:1000'],
            'audience' => ['required', Rule::in(['trabajador', 'cliente', 'todos'])],
            'is_active' => ['boolean'],
            'is_public' => ['boolean'],
            'submit_label' => ['required', 'string', 'max:80'],
            'success_message' => ['required', 'string', 'max:180'],
            'fields' => ['required', 'array', 'min:1'],
            'fields.*.label' => ['required', 'string', 'max:120'],
            'fields.*.key' => ['required', 'string', 'max:80', 'regex:/^[a-z0-9_]+$/'],
            'fields.*.type' => ['required', Rule::in(['text', 'number', 'date', 'select', 'textarea'])],
            'fields.*.required' => ['boolean'],
            'fields.*.options' => ['nullable', 'array'],
            'fields.*.options.*' => ['string', 'max:80'],
        ]);
    }

    private function uniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $base = Str::slug($value) ?: 'formulario';
        $slug = $base;
        $count = 2;

        while (CustomForm::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$base}-{$count}";
            $count++;
        }

        return $slug;
    }
}
