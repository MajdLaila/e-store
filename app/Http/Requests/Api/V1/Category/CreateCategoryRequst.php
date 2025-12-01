<?php

namespace App\Http\Requests\Api\V1\Category;

use App\Http\Requests\Api\V1\BaseApiFormRequest;

class CreateCategoryRequst extends BaseApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * قبل validation نقوم بتحويل القيم "null" أو "" إلى null حقيقي
     * لأن form-data يرسل النص "null" أو سلسلة فارغة أحياناً.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('parent_id')) {
            $parent = $this->input('parent_id');

            if ($parent === 'null' || $parent === '' || is_null($parent)) {
                $this->merge(['parent_id' => null]);
            } else {
                // لو عدت نص رقم نحوله إلى int
                if (is_string($parent) && ctype_digit($parent)) {
                    $this->merge(['parent_id' => (int) $parent]);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'parent_id' => 'nullable|integer|exists:categories,id',
            'name'      => 'required|string|max:255',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'       => 'اسم التصنيف مطلوب.',
            'parent_id.exists'    => 'معرف التصنيف الأب غير موجود.',
            'image.image'         => 'يجب أن يكون الملف صورة.',
            'image.max'           => 'حجم الصورة كبير جداً (الحد الأقصى 2 ميغابايت).',
        ];
    }
}
