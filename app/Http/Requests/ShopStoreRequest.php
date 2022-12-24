<?php

namespace App\Http\Requests;

use App\Enums\Shop\ShopTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class ShopStoreRequest extends FormRequest
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
            'credentials' => ['required', 'array'],
            'credentials.username' => ['required_if:type,'.ShopTypeEnum::SHOPWARE5->value, 'nullable', 'string', 'max:255'],
            'credentials.password' => ['required_if:type,'.ShopTypeEnum::SHOPWARE5->value, 'nullable', 'string', 'max:255'],
            'credentials.client_id' => ['required_if:type,'.ShopTypeEnum::SHOPWARE6->value, 'nullable', 'string', 'max:255'],
            'credentials.client_secret' => ['required_if:type,'.ShopTypeEnum::SHOPWARE6->value, 'nullable', 'string', 'max:255'],
        ];
    }
}
