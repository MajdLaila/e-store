<?php

namespace App\Http\Requests\Api\V1\Ad;

use App\Http\Requests\Api\V1\BaseApiFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateAdRequest extends BaseApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string|max:255',
            'desc'       => 'nullable|string',
            'product_id' => 'required|exists:products,id',
            'image'      => 'nullable|image|max:2048',
        ];
    }
}
