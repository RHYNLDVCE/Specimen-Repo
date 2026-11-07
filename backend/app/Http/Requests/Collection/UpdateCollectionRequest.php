<?php

namespace App\Http\Requests\Collection;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('collection');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', 'unique:mongodb.collections,name,' . $id . ',_id'],
            'description' => ['nullable', 'string'],
            'collection_type' => ['nullable', 'string', 'max:100'],
            'collector' => ['nullable', 'string', 'max:255'],
            'date_started' => ['nullable', 'date'],
            'date_ended' => ['nullable', 'date', 'after_or_equal:date_started'],
            'status' => ['nullable', 'string', 'in:active,archived,closed'],
        ];
    }
}
