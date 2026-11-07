<?php

namespace App\Http\Requests\Researcher;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResearcherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('researcher');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', 'unique:mongodb.researchers,email,' . $id . ',_id'],
            'institution' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'contact_information' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
