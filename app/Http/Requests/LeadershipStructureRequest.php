<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadershipStructureRequest extends FormRequest
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
        $isCreate = $this->isMethod('post');
        $isActive = $this->boolean('is_active');
        $structure = $this->route('leadership_structure');

        return [
            'period_label' => 'required|string|max:100',
            'period_year' => 'required|string|max:50',
            'is_active' => 'sometimes|boolean',
            'general_leader_name' => 'required|string|max:100',
            'general_leader_photo' => ($isCreate || empty($structure?->general_leader_photo_path) ? 'required' : 'nullable') . '|image|max:2048',
            'roles' => 'nullable|array',
            'roles.*.role_id' => 'nullable|uuid',
            'roles.*.title' => 'required_with:roles|string|max:100',
            'roles.*.person_name' => 'required_with:roles|string|max:100',
            'roles.*.photo' => 'nullable|image|max:2048',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->has('is_active') ? $this->boolean('is_active') : false,
        ]);
    }

    public function messages(): array
    {
        return [
            'general_leader_photo.max' => 'Ukuran foto maksimal 2 MB.',
            'roles.*.photo.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $roles = $this->input('roles', []);
            foreach ($roles as $index => $role) {
                $roleId = $role['role_id'] ?? null;
                $hasFile = $this->hasFile("roles.$index.photo");
                if (empty($roleId) && !$hasFile) {
                    $validator->errors()->add("roles.$index.photo", 'Foto wajib diunggah untuk jabatan baru.');
                }
            }
        });
    }
}
