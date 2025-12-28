<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
  public function authorize(): bool
  {
    return auth()->check();
  }

  public function rules(): array
  {
    return [
      'shipping_address' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string', 'max:20'],

      'items' => ['required', 'array', 'min:1'],

      'items.*.product_id' => [
        'required',
        'integer',
        'exists:products,id',
      ],

      'items.*.quantity' => [
        'required',
        'integer',
        'min:1',
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'items.required' => 'Order must contain at least one product.',
      'items.min' => 'Order must contain at least one product.',
      'items.*.product_id.exists' => 'Selected product does not exist.',
      'items.*.quantity.min' => 'Quantity must be at least 1.',
    ];
  }
}
