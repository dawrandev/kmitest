<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StudentStoreRequest extends FormRequest
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
            'login' => 'required|min:8',
            'password' => 'required|min:8|confirmed',
            'full_name' => 'required',
            'phone' => 'required|numeric|digits:9|unique:students,phone',
            'address' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required' => 'Login kiritilishi shart.',
            'login.min' => 'Login kamida 8 ta belgidan iborat bo‘lishi kerak.',

            'password.required' => 'Parol kiritilishi shart.',
            'password.min' => 'Parol kamida 8 ta belgidan iborat bo‘lishi kerak.',
            'password.confirmed' => 'Parol tasdiqlash bilan mos kelmadi.',

            'full_name.required' => 'To‘liq ism kiritilishi shart.',

            'phone.required' => 'Telefon raqami kiritilishi shart.',
            'phone.numeric' => 'Telefon raqami faqat raqamlardan iborat bo‘lishi kerak.',
            'phone.digits' => 'Telefon raqami 9 ta raqamdan iborat bo‘lishi kerak.',
            'phone.unique' => 'Bu telefon raqami allaqachon ro‘yxatdan o‘tgan.',

            'address.required' => 'Manzil kiritilishi shart.',
        ];
    }
}
