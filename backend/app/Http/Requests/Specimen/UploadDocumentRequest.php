<?php

namespace App\Http\Requests\Specimen;

use Illuminate\Foundation\Http\FormRequest;

class UploadDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document' => ['required', 'file', 'mimes:pdf,doc,docx,txt,csv,xlsx', 'max:20480'],
            'document_type' => ['nullable', 'string', 'in:field_notes,dna_sequence,permits,publication,report,other'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }
}
