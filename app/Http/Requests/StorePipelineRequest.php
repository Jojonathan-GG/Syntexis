<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePipelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'connection_id' => ['required', 'exists:connections,id'],
            'dataset_id' => ['nullable', 'exists:datasets,id'],
            'schedule' => ['required', 'in:manual,hourly,daily'],
            'status' => ['required', 'in:active,paused'],
            'rows' => ['required', 'integer', 'min:1', 'max:2000'],
            'mapping_mode' => ['required', 'in:auto,custom'],
        ];
    }
}
