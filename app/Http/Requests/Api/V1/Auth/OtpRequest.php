<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use App\Http\Requests\Api\V1\BaseApiFormRequest;

class OtpRequest extends BaseApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'code' => 'required|string|size:6|regex:/^[0-9]{6}$/',
            'purpose' => 'required|in:register,login,reset_password,verify_email',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني لا يجب أن يتجاوز 255 حرف',
            
            'code.required' => 'كود التحقق مطلوب',
            'code.size' => 'كود التحقق يجب أن يكون 6 أرقام',
            'code.regex' => 'كود التحقق يجب أن يحتوي على أرقام فقط',
            
            'purpose.required' => 'الغرض من التحقق مطلوب',
            'purpose.in' => 'الغرض من التحقق غير صحيح',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'البريد الإلكتروني',
            'code' => 'كود التحقق',
            'purpose' => 'الغرض من التحقق',
        ];
    }
}
