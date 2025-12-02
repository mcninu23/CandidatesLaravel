<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Aquí podrías meter auth/roles si hiciera falta.
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'        => ['required', 'string', 'max:255'],
            'email'            => ['required', 'string', 'email', 'max:255'],
            'years_experience' => ['required', 'integer', 'min:0', 'max:60'],
            'cv_text'          => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'        => 'El nombre completo es obligatorio.',
            'email.required'            => 'El email es obligatorio.',
            'email.email'               => 'El email no tiene un formato válido.',
            'years_experience.required' => 'Los años de experiencia son obligatorios.',
            'years_experience.integer'  => 'Los años de experiencia deben ser un número entero.',
            'cv_text.required'          => 'El CV es obligatorio.',
        ];
    }
}
