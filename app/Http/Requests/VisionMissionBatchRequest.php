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
            'missions' => 'required|array|min:1',
            'missions.*.title' => 'nullable|string|max:150',
            'missions.*.content' => 'required|string',
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
