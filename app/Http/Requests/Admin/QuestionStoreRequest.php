<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class QuestionStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'image' => 'nullable',
            'questions' => 'required|array|min:1',
            'questions.*' => 'required|string|min:3',
            'answers' => 'required|array|min:1',
            'answers.*' => 'required|array|min:1',
            'answers.*.*' => 'required|array',
            'answers.*.*.text' => 'required|string|min:1',
            'answers.*.*.answer_id' => 'required|string',
        ];

        // Dinamik ravishda correct_answer_* rules qo'shamiz
        $questions = $this->input('questions', []);
        foreach (array_keys($questions) as $index => $questionId) {
            $rules["correct_answer_" . ($index + 1)] = 'required|string';
        }

        return $rules;
    }

    public function messages()
    {
        $messages = [
            'image.file' => 'Yuklangan fayl noto\'g\'ri formatda',
            'image.mimes' => 'Faqat JPG, PNG, JPEG, GIF, WEBP formatdagi rasmlar qabul qilinadi',
            'image.max' => 'Rasm hajmi 5MB dan oshmasligi kerak',
            'questions.required' => 'Kamida bitta savol kiritilishi shart',
            'questions.*.required' => 'Savol matni kiritilishi shart',
            'answers.required' => 'Kamida bitta javob kiritilishi shart',
            'answers.*.*.text.required' => 'Javob matni kiritilishi shart',
        ];

        // Dinamik correct_answer messages
        $questions = $this->input('questions', []);
        foreach (array_keys($questions) as $index => $questionId) {
            $messages["correct_answer_" . ($index + 1) . ".required"] = ($index + 1) . "-savol uchun to'g'ri javob tanlanishi shart";
        }

        return $messages;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $questions = $this->input('questions', []);

            // Har bir savol uchun correct_answer mavjudligini tekshiramiz
            foreach (array_keys($questions) as $index => $questionId) {
                $correctAnswerKey = "correct_answer_" . ($index + 1);

                if (!$this->has($correctAnswerKey) || empty($this->input($correctAnswerKey))) {
                    $validator->errors()->add(
                        $correctAnswerKey,
                        ($index + 1) . "-savol uchun to'g'ri javob tanlanishi shart"
                    );
                }
            }
        });
    }
}
