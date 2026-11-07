<?php

namespace App\Http\Requests\Collection;

use Illuminate\Foundation\Http\FormRequest;

class StoreCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:mongodb.collections,name'],
            'description' => ['nullable', 'string'],
            'collection_type' => ['nullable', 'string', 'max:100'],
            'collector' => ['nullable', 'string', 'max:255'],
            'date_started' => ['nullable', 'date'],
            'date_ended' => ['nullable', 'date', 'after_or_equal:date_started'],
            'status' => ['nullable', 'string', 'in:active,archived,closed'],
        ];
    }
}
