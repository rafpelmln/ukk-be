<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VisionMissionBatchRequest extends FormRequest
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
            'vision_title' => 'required|string|max:150',
            'vision_content' => 'required|string',
            'vision_is_active' => 'sometimes|boolean',
            'mission_title' => 'nullable|string|max:150',
            'mission_items' => 'required|array|min:1',
            'mission_items.*' => 'required|string',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'vision_is_active' => $this->has('vision_is_active')
                ? filter_var($this->input('vision_is_active'), FILTER_VALIDATE_BOOLEAN)
                : true,
        ]);
    }
}
