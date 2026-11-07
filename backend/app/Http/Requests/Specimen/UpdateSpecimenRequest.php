<?php

namespace App\Http\Requests\Specimen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSpecimenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('speciman');

        return [
            'specimen_code' => ['sometimes', 'required', 'string', 'max:50', 'unique:mongodb.specimens,specimen_code,' . $id . ',_id'],
            'catalog_number' => ['nullable', 'string', 'max:50'],
            'scientific_name' => ['sometimes', 'required', 'string', 'max:255'],
            'common_name' => ['nullable', 'string', 'max:255'],
            'kingdom' => ['nullable', 'string', 'max:100'],
            'phylum' => ['nullable', 'string', 'max:100'],
            'class' => ['nullable', 'string', 'max:100'],
            'order' => ['nullable', 'string', 'max:100'],
            'family' => ['nullable', 'string', 'max:100'],
            'genus' => ['nullable', 'string', 'max:100'],
            'species' => ['nullable', 'string', 'max:100'],
            'taxon_id' => ['nullable', 'string'],
            'collection_id' => ['nullable', 'string'],
            'project_id' => ['nullable', 'string'],
            'collector' => ['nullable', 'string', 'max:255'],
            'collection_date' => ['nullable', 'date'],
            'location_id' => ['nullable', 'string'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'habitat' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'preservation_method' => ['nullable', 'string', 'in:wet,dry,pinned,slide,herbarium_sheet,frozen,skeletal,other'],
            'repository_location' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'string', 'in:active,loaned,damaged,lost,deaccessioned'],
        ];
    }
}
