<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignEvaluatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Por ahora permitimos todo, aquí podrías meter auth/roles en el futuro.
        return true;
    }

    public function rules(): array
    {
        return [
            'evaluator_id' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'evaluator_id.required' => 'El campo evaluator_id es obligatorio.',
            'evaluator_id.integer'  => 'El campo evaluator_id debe ser un número entero.',
            'evaluator_id.min'      => 'El campo evaluator_id debe ser un ID válido.',
        ];
    }
}
