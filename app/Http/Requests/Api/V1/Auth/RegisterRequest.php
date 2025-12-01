<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

use App\Http\Requests\Api\V1\BaseApiFormRequest;

class RegisterRequest extends BaseApiFormRequest
{
    // مثال على ال body المطلوب إرساله مع طلب التسجيل:
    /*
    {
        "name": "Yousef Ahmed",
        "email": "user@example.com",
        "password": "Secret123",
        "password_confirmation": "Secret123",
        "phone": "+963912345678",
        "avatar": "uploads/avatars/1234.jpg",
        "is_active": true,
        "is_admin": false,
        "lat": 33.5138,
        "lang": 36.2765,
        "email_verified_at": "2024-06-01 12:00:00",
        "phone_verified_at": "2024-06-01 12:10:00"
    }
    */

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users,email',

            // كلمة المرور مطلوبة بالتسجيل الاعتيادي
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required|string|min:8',

            // اختياري
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'avatar' => 'nullable|string|max:2048', // URL أو مسار

            // الحقول المنطقية
            'is_active' => 'sometimes|boolean',

            // ملاحظة أمنية: السماح بهذا الحقل يعرضك لرفع صلاحيات ذاتي.
            // يفضل تجاهله في الإنتاج أو جعله فقط للإدمن.
            'is_admin' => 'sometimes|boolean',

            // الإحداثيات
            'lat' => 'nullable|numeric|between:-90,90',
            'lang' => 'nullable|numeric|between:-180,180',

            // إن أردت قبول أختام التحقق (غير مفضّل عادة أثناء التسجيل)
            'email_verified_at' => 'nullable|date',
            'phone_verified_at' => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.min' => 'الاسم يجب أن يكون على الأقل حرفين',
            'name.max' => 'الاسم لا يجب أن يتجاوز 255 حرف',

            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل',
            'email.max' => 'البريد الإلكتروني لا يجب أن يتجاوز 255 حرف',

            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون على الأقل 8 أحرف',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password.regex' => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير ورقم',

            'password_confirmation.required' => 'تأكيد كلمة المرور مطلوب',
            'password_confirmation.min' => 'تأكيد كلمة المرور يجب أن يكون على الأقل 8 أحرف',

            'phone.regex' => 'رقم الهاتف غير صحيح',
            'phone.max' => 'رقم الهاتف لا يجب أن يتجاوز 20 رقم',

            'avatar.max' => 'رابط الصورة لا يجب أن يتجاوز 2048 حرف',

            'is_active.boolean' => 'قيمة التفعيل يجب أن تكون true أو false',
            'is_admin.boolean' => 'قيمة الأدمن يجب أن تكون true أو false',

            'lat.numeric' => 'خط العرض يجب أن يكون رقمًا',
            'lat.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'lang.numeric' => 'خط الطول يجب أن يكون رقمًا',
            'lang.between' => 'خط الطول يجب أن يكون بين -180 و 180',

            'email_verified_at.date' => 'تاريخ تحقق الإيميل غير صالح',
            'phone_verified_at.date' => 'تاريخ تحقق الهاتف غير صالح',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة المرور',
            'password_confirmation' => 'تأكيد كلمة المرور',
            'phone' => 'رقم الهاتف',
            'avatar' => 'الصورة',
            'is_active' => 'الحساب مفعّل',
            'is_admin' => 'حساب إداري',
            'lat' => 'خط العرض',
            'lang' => 'خط الطول',
            'email_verified_at' => 'تاريخ تحقق البريد',
            'phone_verified_at' => 'تاريخ تحقق الهاتف',
        ];
    }
}
