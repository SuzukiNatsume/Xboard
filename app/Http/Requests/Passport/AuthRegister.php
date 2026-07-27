<?php

namespace App\Http\Requests\Passport;

use Illuminate\Foundation\Http\FormRequest;

class AuthRegister extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'email:strict',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!preg_match('/^[^@\s]+@mails\.ucas\.ac\.cn$/i', trim((string) $value))) {
                        $fail(__('Registration is limited to @mails.ucas.ac.cn email addresses'));
                    }
                },
            ],
            'password' => 'required|min:8'
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('Email can not be empty'),
            'email.email' => __('Email format is incorrect'),
            'password.required' => __('Password can not be empty'),
            'password.min' => __('Password must be greater than 8 digits')
        ];
    }
}
