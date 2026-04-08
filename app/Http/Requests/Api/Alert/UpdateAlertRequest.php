<?php

namespace App\Http\Requests\Api\Alert;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAlertRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reporter_name' => 'sometimes|string|max:255',
            'content' => 'sometimes|string|max:5000',
            'type' => ['sometimes', Rule::in(['Urgence', 'Information', 'Alerte', 'Autre'])],
            'urgency_level' => ['sometimes', Rule::in(['Faible', 'Moyen', 'Critique'])],
            'channels' => 'sometimes|array',
            'channels.*' => 'string',
            'scheduled_at' => 'nullable|date|after:now',
            'recurrence' => 'nullable|array',
            'recurrence.type' => 'sometimes|string|in:daily,weekly,monthly',
            'recurrence.interval' => 'sometimes|integer|min:1',
        ];
    }
}
