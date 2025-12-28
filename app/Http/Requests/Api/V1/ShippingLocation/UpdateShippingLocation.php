<?php

namespace App\Http\Requests\Api\V1\ShippingLocation;

use App\Http\Requests\Api\V1\BaseApiFormRequest;

class UpdateShippingLocation extends BaseApiFormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'lat' => ['required', 'numeric'],   // يجب أن يكون رقم
      'lang' => ['required', 'numeric'],  // يجب أن يكون رقم
      'phone' => ['nullable', 'string', 'max:20'], // يمكن أن يبدأ بـ 0
      'address' => ['nullable', 'string', 'max:255'],
    ];
  }
}
