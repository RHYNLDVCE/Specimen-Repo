<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_code' => ['required', 'string', 'max:50', 'unique:mongodb.projects,project_code'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'principal_researcher_id' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'string', 'in:planning,active,completed,suspended'],
        ];
    }
}
