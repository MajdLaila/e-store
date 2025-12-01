<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use App\Http\Requests\Api\V1\BaseApiFormRequest;

class SendOtpRequest extends BaseApiFormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'purpose' => 'sometimes|in:register,login,reset_password,verify_email',
            'resend' => 'sometimes|boolean', // اختياري لإعادة الإرسال
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني لا يجب أن يتجاوز 255 حرف',
            
            'purpose.in' => 'الغرض من التحقق غير صحيح. القيم المسموحة: register, login, reset_password, verify_email',
            
            'resend.boolean' => 'قيمة إعادة الإرسال يجب أن تكون true أو false',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'البريد الإلكتروني',
            'purpose' => 'الغرض من التحقق',
            'resend' => 'إعادة الإرسال',
        ];
    }
}
