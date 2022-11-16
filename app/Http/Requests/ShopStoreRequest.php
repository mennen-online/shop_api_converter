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
            'credentials' => ['required', 'array', 'min:2', 'max:2'],
            'credentials.api_key' => ['required', 'string', 'max:255'],
            'credentials.api_secret' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'exists:users,id'],
        ];
    }
}
