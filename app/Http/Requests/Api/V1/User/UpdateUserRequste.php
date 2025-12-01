<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\User;

use App\Http\Requests\Api\V1\BaseApiFormRequest;

class UpdateUserRequste extends BaseApiFormRequest
{
    public function rules(): array
    {
        // راعي يستطيع التعديل على جميع الحقول عدا الايميل وكلمة السر
        return [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|string|max:255',
            'is_active' => 'sometimes|boolean',
            'is_admin' => 'sometimes|boolean',
            'lat' => 'sometimes|nullable|numeric|between:-90,90',
            'lang' => 'sometimes|nullable|numeric|between:-180,180',
            // لا يسمح بتعديل: email, password
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.string' => 'الاسم يجب أن يكون نصاً',
            'name.max' => 'الاسم يجب ألا يزيد عن 255 حرفاً',

            'phone.string' => 'رقم الهاتف يجب أن يكون نصاً',
            'phone.max' => 'رقم الهاتف يجب ألا يزيد عن 20 رقم/حرف',

            'avatar.string' => 'الصورة الرمزية يجب أن تكون نصاً (رابط)',
            'avatar.max' => 'الصورة الرمزية يجب ألا يزيد عن 255 حرفاً',

            'is_active.boolean' => 'حالة التفعيل يجب أن تكون صح أو خطأ',
            'is_admin.boolean' => 'حقل الإدارة يجب أن يكون صح أو خطأ',

            'lat.numeric' => 'يجب أن يكون خط العرض رقمياً',
            'lat.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'lang.numeric' => 'يجب أن يكون خط الطول رقمياً',
            'lang.between' => 'خط الطول يجب أن يكون بين -180 و 180',

            // لا توجد رسائل للبريد الالكتروني أو كلمة السر لأنهم غير قابلين للتعديل هنا
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'phone' => 'رقم الهاتف',
            'avatar' => 'الصورة الرمزية',
            'is_active' => 'حالة التفعيل',
            'is_admin' => 'حساب إداري',
            'lat' => 'خط العرض',
            'lang' => 'خط الطول',
        ];
    }
}
