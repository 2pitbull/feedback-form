<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use App\Services\CaptchaService;
use Illuminate\Validation\Validator;

class StoreFeedbackRequest extends FormRequest
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
            'action' => ['required', 'string', 'in:message'],
            'subject' => [
                'required',
                'string',
                'in:consultation,technical,cooperation,other',
            ],
            'name' => ['required', 'string', 'max:255'],
            'phone' => [
                'required',
                'string',
                'max:255',
                'regex:/^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/',
            ],
            'email' => ['required', 'string', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:4096'],
            'captcha_answer' => ['required', 'string', 'max:255'],
            'personal_data_consent' => ['required', 'accepted'],
            'website' => ['nullable', 'prohibited'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'Некорректное действие формы.',
            'action.in' => 'Некорректное действие формы.',

            'subject.required' => 'Выберите тему обращения.',
            'subject.in' => 'Выберите тему из списка.',

            'name.required' => 'Укажите ФИО.',
            'name.max' => 'ФИО не должно превышать 255 символов.',

            'phone.required' => 'Укажите телефон.',
            'phone.max' => 'Телефон не должен превышать 255 символов.',
            'phone.regex' => 'Введите телефон в формате +7 (999) 123-45-67.',

            'email.required' => 'Укажите e-mail.',
            'email.email' => 'Введите корректный адрес электронной почты.',
            'email.max' => 'E-mail не должен превышать 255 символов.',

            'message.required' => 'Введите сообщение.',
            'message.max' => 'Сообщение не должно превышать 4096 символов.',

            'captcha_answer.required' => 'Введите ответ на CAPTCHA.',
            'captcha_answer.max' => 'Ответ CAPTCHA слишком длинный.',

            'personal_data_consent.required' => 'Необходимо согласие на обработку персональных данных.',
            'personal_data_consent.accepted' => 'Необходимо согласие на обработку персональных данных.',

            'website.prohibited' => 'Не удалось обработать запрос.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('captcha_answer')) {
                    return;
                }

                $answer = $this->input('captcha_answer');

                if (
                    ! is_string($answer)
                    || ! app(CaptchaService::class)->verify($answer)
                ) {
                    $validator->errors()->add(
                        'captcha_answer',
                        'Неверный ответ CAPTCHA.',
                    );
                }
            },
        ];
    }
}
