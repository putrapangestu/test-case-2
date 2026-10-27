<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class UpdateCourierRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $courier = $this->route('courier');
        $courierId = is_object($courier) ? $courier->id : $courier;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique('couriers', 'email')->ignore($courierId)],
            'phone_number' => ['sometimes', 'required', 'string', 'max:25', Rule::unique('couriers', 'phone_number')->ignore($courierId)],
            'vehicle_type' => ['nullable', 'string', 'max:100'],
            'license_plate' => ['nullable', 'string', 'max:50'],
            'level' => ['sometimes', 'required', 'integer', 'between:1,5'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kurir wajib diisi.',
            'name.string' => 'Nama kurir harus berupa teks.',
            'name.max' => 'Nama kurir maksimal 255 karakter.',
            'email.required' => 'Email kurir wajib diisi.',
            'email.email' => 'Format email kurir tidak valid.',
            'email.unique' => 'Email kurir sudah digunakan.',
            'email.max' => 'Email kurir maksimal 255 karakter.',
            'phone_number.required' => 'Nomor telepon kurir wajib diisi.',
            'phone_number.unique' => 'Nomor telepon kurir sudah digunakan.',
            'phone_number.max' => 'Nomor telepon kurir maksimal 25 karakter.',
            'vehicle_type.string' => 'Jenis kendaraan harus berupa teks.',
            'vehicle_type.max' => 'Jenis kendaraan maksimal 100 karakter.',
            'license_plate.string' => 'Plat nomor harus berupa teks.',
            'license_plate.max' => 'Plat nomor maksimal 50 karakter.',
            'level.required' => 'Level kurir wajib diisi.',
            'level.integer' => 'Level kurir harus berupa angka.',
            'level.between' => 'Level kurir harus berada di antara level 1 hingga 5.',
            'is_active.boolean' => 'Status aktif kurir harus berupa boolean (true/false).',
        ];
    }
}
