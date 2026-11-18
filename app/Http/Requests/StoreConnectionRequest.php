<?php

namespace App\Http\Requests;

use App\Services\ConnectorCatalog;
use Illuminate\Foundation\Http\FormRequest;

class StoreConnectionRequest extends FormRequest
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
            'type' => ['required', 'in:'.implode(',', ConnectorCatalog::types())],
            'status' => ['required', 'in:active,paused'],
            'config' => ['nullable', 'array'],
            'config.endpoint' => ['nullable', 'url'],
        ];
    }
}
