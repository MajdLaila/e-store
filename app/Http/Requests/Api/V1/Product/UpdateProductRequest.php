<?php

namespace App\Http\Requests\Api\V1\Product;

use App\Http\Requests\Api\V1\BaseApiFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends BaseApiFormRequest
{
  /**
   * Example body:
   *
   * {
   *   "name": "منتج 1",
   *   "description": "وصف المنتج...",
   *   "category_id": 2,
   *   "is_valid": true,
   *   "price": 120.50,
   *   "is_show": true,
   *   "hot_price": 99.99,
   *   "stock": 20
   * }
   */



  public function rules()
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'description' => ['sometimes', 'nullable', 'string'],
      'category_id' => ['sometimes', 'exists:categories,id'],
      'is_valid' => ['sometimes', 'boolean'],
      'price' => ['sometimes', 'numeric', 'min:0'],
      'is_show' => ['sometimes', 'boolean'],
      'hot_price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
      'stock' => ['sometimes', 'integer', 'min:0'],
      'images.*' => ['sometimes', 'file', 'image'],
      'remove_image_ids.*' => ['sometimes', 'integer', 'exists:product_images,id'],
      'main_image_id' => ['sometimes', 'integer', 'exists:product_images,id'],
    ];
  }


  public function messages()
  {
    return [
      'name.required' => 'حقل الاسم مطلوب.',
      'name.string' => 'حقل الاسم يجب أن يكون نصاً.',
      'name.max' => 'حقل الاسم يجب ألّا يتجاوز 255 حرفًا.',

      'description.string' => 'حقل الوصف يجب أن يكون نصاً.',

      'category_id.required' => 'حقل التصنيف مطلوب.',
      'category_id.exists' => 'التصنيف المحدد غير موجود.',

      'is_valid.required' => 'حقل الصلاحية مطلوب.',
      'is_valid.boolean' => 'حقل الصلاحية يجب أن يكون صحيح أو خطأ.',

      'price.required' => 'حقل السعر مطلوب.',
      'price.numeric' => 'حقل السعر يجب أن يكون رقماً.',
      'price.min' => 'قيمة السعر يجب أن تكون أكبر من أو تساوي صفر.',

      'is_show.required' => 'حقل الظهور مطلوب.',
      'is_show.boolean' => 'حقل الظهور يجب أن يكون صحيح أو خطأ.',

      'hot_price.numeric' => 'حقل السعر المخفض يجب أن يكون رقماً.',
      'hot_price.min' => 'قيمة السعر المخفض يجب أن تكون أكبر من أو تساوي صفر.',

      'stock.required' => 'حقل المخزون مطلوب.',
      'stock.integer' => 'حقل المخزون يجب أن يكون عدداً صحيحاً.',
      'stock.min' => 'قيمة المخزون يجب أن تكون أكبر من أو تساوي صفر.',
    ];
  }
}
