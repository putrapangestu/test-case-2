<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FilterCourierRequest extends FormRequest
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
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'level' => ['nullable', 'string'],
            'sort_by' => ['nullable', 'string', 'in:name,created_at,level,id'],
            'sort_order' => ['nullable', 'string', 'in:asc,desc,ASC,DESC'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
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
            'search.string' => 'Parameter pencarian harus berupa string.',
            'search.max' => 'Parameter pencarian maksimal 255 karakter.',
            'sort_by.in' => 'Kolom pengurutan yang diperbolehkan hanya name, created_at, level, atau id.',
            'sort_order.in' => 'Arah pengurutan harus berupa asc atau desc.',
            'per_page.integer' => 'Jumlah data per halaman harus berupa integer.',
            'per_page.min' => 'Jumlah data per halaman minimal 1.',
            'per_page.max' => 'Jumlah data per halaman maksimal 100.',
        ];
    }
}
