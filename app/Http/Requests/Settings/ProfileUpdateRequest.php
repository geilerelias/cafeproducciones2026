<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower((string) $this->input('email')),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'identification_type' => ['required', 'string', Rule::in(User::identificationTypeKeys())],
            'identification_number' => [
                'required',
                'string',
                'min:5',
                'max:30',
                'regex:/^[A-Za-z0-9.\-\s]+$/',
                Rule::unique('users', 'identification_number')
                    ->where(fn ($query) => $query->where('identification_type', $this->input('identification_type')))
                    ->ignore($this->user()->id),
            ],
            'phone' => ['required', 'string', 'max:20'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...User::identificationMessages(),
            'phone.required' => 'Ingresa un telefono de contacto.',
            'phone.max' => 'El telefono no puede superar 20 caracteres.',
        ];
    }
}
