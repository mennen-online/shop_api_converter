<?php

namespace App\Http\Requests;

use App\Enums\Shop\ShopTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ShopUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'max:255', 'string'],
            'type' => ['required', new Enum(ShopTypeEnum::class)],
            'url' => ['required', 'url'],
            'credentials' => ['required', 'max:255', 'json'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
