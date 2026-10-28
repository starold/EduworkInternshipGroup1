<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fuel_level' => ['nullable', 'string', 'in:empty,quarter,half,three_quarter,full'],
            'condition_status' => ['nullable', 'string', 'in:good,minor_damage,damaged'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }
}
