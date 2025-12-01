<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Auth;

 
use App\Http\Requests\Api\V1\BaseApiFormRequest;

class ProfileUpdateRequest extends BaseApiFormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255|min:2',
            'phone' => 'nullable|string|max:20|regex:/^[\+]?[0-9\s\-\(\)]+$/',
            'avatar' => 'nullable|string|max:2048',
            'lat' => 'nullable|numeric|between:-90,90',
            'lang' => 'nullable|numeric|between:-180,180',
        ];
    }

    public function messages(): array
    {
        return [
            'name.min' => 'الاسم يجب أن يكون على الأقل حرفين',
            'name.max' => 'الاسم لا يجب أن يتجاوز 255 حرف',
            'phone.regex' => 'رقم الهاتف غير صحيح',
            'phone.max' => 'رقم الهاتف لا يجب أن يتجاوز 20 رقم',
            'avatar.max' => 'رابط الصورة لا يجب أن يتجاوز 2048 حرف',
            'lat.numeric' => 'خط العرض يجب أن يكون رقمًا',
            'lat.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'lang.numeric' => 'خط الطول يجب أن يكون رقمًا',
            'lang.between' => 'خط الطول يجب أن يكون بين -180 و 180',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'phone' => 'رقم الهاتف',
            'avatar' => 'الصورة',
            'lat' => 'خط العرض',
            'lang' => 'خط الطول',
        ];
    }
}
