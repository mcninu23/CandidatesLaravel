<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsolidatedListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page'      => ['sometimes', 'integer', 'min:1'],
            'per_page'  => ['sometimes', 'integer', 'min:1', 'max:100'],
            'order_by'  => ['sometimes', 'string'],
            'order_dir' => ['sometimes', 'in:asc,desc'],

            // Filtros opcionales
            'full_name'        => ['sometimes', 'string'],
            'email'            => ['sometimes', 'string'],
            'evaluator_name'   => ['sometimes', 'string'],
            'years_experience' => ['sometimes', 'integer'],
        ];
    }

    public function sanitized(): array
    {
        return [
            'page'      => $this->input('page', 1),
            'per_page'  => $this->input('per_page', 20),
            'order_by'  => $this->input('order_by', 'years_experience'),
            'order_dir' => $this->input('order_dir', 'desc'),
            'filters'   => $this->only([
                'full_name',
                'email',
                'evaluator_name',
                'years_experience'
            ]),
        ];
    }
}
