<?php

namespace App\Http\Requests\Taxon;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('taxa') ?? $this->route('taxon');

        return [
            'scientific_name' => ['sometimes', 'required', 'string', 'max:255', 'unique:mongodb.taxa,scientific_name,' . $id . ',_id'],
            'kingdom' => ['nullable', 'string', 'max:100'],
            'phylum' => ['nullable', 'string', 'max:100'],
            'class' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'string', 'max:100'],
            'family' => ['nullable', 'string', 'max:100'],
            'genus' => ['nullable', 'string', 'max:100'],
            'species' => ['nullable', 'string', 'max:100'],
            'common_name' => ['nullable', 'string', 'max:255'],
            'authority' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
