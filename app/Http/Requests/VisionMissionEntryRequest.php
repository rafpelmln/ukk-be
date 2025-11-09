<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisionMissionEntryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:vision,mission',
            'title' => 'required|string|max:150',
            'content' => 'required|string',
            'is_active' => 'sometimes|boolean',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active')
                ? filter_var($this->input('is_active'), FILTER_VALIDATE_BOOLEAN)
                : false,
        ]);
    }
}
