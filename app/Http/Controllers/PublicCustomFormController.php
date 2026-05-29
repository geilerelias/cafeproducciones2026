<?php

namespace App\Http\Controllers;

use App\Models\CustomForm;
use App\Support\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class PublicCustomFormController extends Controller
{
    public function show(CustomForm $customForm): Response
    {
        abort_unless($customForm->is_active && $customForm->is_public, 404);

        return Inertia::render('Forms/Show', [
            'formDefinition' => $customForm,
        ])->withViewData('meta', [
            'title' => $customForm->title.' | CAFE Producciones',
            'description' => $customForm->description ?: 'Formulario publico de CAFE Producciones.',
            'image' => asset('images/seo-logo.png'),
            'url' => route('forms.public.show', $customForm),
        ]);
    }

    public function submit(Request $request, CustomForm $customForm): RedirectResponse
    {
        abort_unless($customForm->is_active && $customForm->is_public, 404);

        $rules = [];

        foreach ($customForm->fields as $field) {
            $fieldRules = [];
            $fieldRules[] = ! empty($field['required']) ? 'required' : 'nullable';

            $fieldRules[] = match ($field['type']) {
                'number' => 'numeric',
                'date' => 'date',
                default => 'string',
            };

            if ($field['type'] === 'select') {
                $fieldRules[] = Rule::in($field['options'] ?? []);
            } else {
                $fieldRules[] = 'max:2000';
            }

            $rules['answers.'.$field['key']] = $fieldRules;
        }

        $validated = $request->validate($rules);

        $customForm->submissions()->create([
            'user_id' => $request->user()?->id,
            'answers' => $validated['answers'] ?? [],
        ]);

        return Feedback::success($customForm->success_message, 'Respuesta enviada');
    }
}
