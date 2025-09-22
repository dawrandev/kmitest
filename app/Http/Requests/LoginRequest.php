<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'login' => 'required|string',
            'password' => 'required|string|min:8',
        ];
    }

    public function messages()
    {
        return [
            'login.required' => 'Login kiritilishi shart',
            'login.string' => 'Login faqat matn bo`lishi mumkin',
            'password.required' => 'Parol kiritilishi shart',
            'password.string' => 'Parol faqat matn bo`lishi mumkin',
            'password.min' => 'Parol uzunligi kamida 8 ta belgidan iborat bo`lishi kerak',
        ];
    }
}
