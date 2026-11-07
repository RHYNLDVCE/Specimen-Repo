<?php

namespace App\Http\Requests\Specimen;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
            'caption' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'in:dorsal,ventral,lateral,habitat,microscopic,other'],
        ];
    }
}
