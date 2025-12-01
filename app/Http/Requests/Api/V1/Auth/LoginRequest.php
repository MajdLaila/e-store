<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

 use App\Http\Requests\Api\V1\BaseApiFormRequest;

class LoginRequest extends BaseApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني لا يجب أن يتجاوز 255 حرف',
            
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور مطلوبة',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
        ];
    }
}
