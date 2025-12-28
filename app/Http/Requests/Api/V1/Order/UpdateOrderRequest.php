<?php

namespace App\Http\Requests\Api\V1\Order;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Order;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'nullable', 'string', 'max:255'],
      'description' => ['sometimes', 'nullable', 'string'],
      'quantity' => ['sometimes', 'integer', 'min:1'],
      'total_price' => ['sometimes', 'numeric', 'min:0'],
      'shipping_address' => ['sometimes', 'nullable', 'string', 'max:255'],
      'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
      'status' => [
        'sometimes',
        Rule::in([
          Order::STATUS_PENDING,
          Order::STATUS_PROCESSING,
          Order::STATUS_SHIPPED,
          Order::STATUS_DELIVERED,
          Order::STATUS_CANCELLED,
        ]),
      ],
    ];
  }
}
