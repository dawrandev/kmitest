<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StudentUpdateRequest extends FormRequest
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
            'phone' => 'required|numeric|digits:9',
            'address' => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'   => __('Login maydoni majburiy.'),
            'login.min'        => __('Login kamida :min ta belgidan iborat bo‘lishi kerak.'),

            'password.required'  => __('Parol maydoni majburiy.'),
            'password.min'       => __('Parol kamida :min ta belgidan iborat bo‘lishi kerak.'),
            'password.confirmed' => __('Parol tasdiqlash bilan mos kelmadi.'),

            'full_name.required' => __('To‘liq ism maydoni majburiy.'),

            'phone.required' => __('Telefon raqami majburiy.'),
            'phone.numeric'  => __('Telefon raqami faqat raqamlardan iborat bo‘lishi kerak.'),
            'phone.digits'   => __('Telefon raqami aniq :digits ta raqamdan iborat bo‘lishi kerak.'),

            'address.required' => __('Manzil maydoni majburiy.'),
        ];
    }
}
