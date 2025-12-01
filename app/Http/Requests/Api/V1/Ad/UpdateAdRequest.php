<?php

namespace App\Http\Requests\Api\V1\Ad;

use App\Http\Requests\Api\V1\BaseApiFormRequest;
 
class UpdateAdRequest extends BaseApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'nullable|string|max:255',
            'desc'       => 'nullable|string',
            'product_id' => 'nullable|exists:products,id',
            'image'      => 'nullable|image|max:2048',
        ];
    }
}
