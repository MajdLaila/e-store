<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
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
        $provider = $this->route('provider') ?? $this->input('provider');
        
        $rules = [
            'email' => 'required|email|max:255',
            'name' => 'required|string|max:255|min:2',
            'avatar' => 'nullable|string|url|max:500',
        ];

        // Add provider-specific rules
        if ($provider === 'google') {
            $rules['google_id'] = 'required|string|max:255';
        } elseif ($provider === 'facebook') {
            $rules['facebook_id'] = 'required|string|max:255';
        } else {
            // Default rules for general social login
            $rules['provider'] = 'required|in:google,facebook';
            $rules['provider_id'] = 'required|string|max:255';
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.max' => 'البريد الإلكتروني لا يجب أن يتجاوز 255 حرف',
            
            'name.required' => 'الاسم مطلوب',
            'name.min' => 'الاسم يجب أن يكون على الأقل حرفين',
            'name.max' => 'الاسم لا يجب أن يتجاوز 255 حرف',
            
            'avatar.url' => 'رابط الصورة الشخصية غير صحيح',
            'avatar.max' => 'رابط الصورة الشخصية لا يجب أن يتجاوز 500 حرف',
            
            'google_id.required' => 'معرف Google مطلوب',
            'google_id.max' => 'معرف Google لا يجب أن يتجاوز 255 حرف',
            
            'facebook_id.required' => 'معرف Facebook مطلوب',
            'facebook_id.max' => 'معرف Facebook لا يجب أن يتجاوز 255 حرف',
            
            'provider.required' => 'مزود الخدمة مطلوب',
            'provider.in' => 'مزود الخدمة غير مدعوم',
            
            'provider_id.required' => 'معرف المزود مطلوب',
            'provider_id.max' => 'معرف المزود لا يجب أن يتجاوز 255 حرف',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'البريد الإلكتروني',
            'name' => 'الاسم',
            'avatar' => 'الصورة الشخصية',
            'google_id' => 'معرف Google',
            'facebook_id' => 'معرف Facebook',
            'provider' => 'مزود الخدمة',
            'provider_id' => 'معرف المزود',
        ];
    }
}
