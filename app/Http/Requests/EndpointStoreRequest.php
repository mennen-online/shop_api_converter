<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EndpointStoreRequest extends FormRequest
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
            'shop_id' => ['required', 'exists:shops,id'],
            'name' => ['required', 'max:255', 'string'],
            'url' => ['required', 'url'],
            'entity_id' => ['required', 'exists:entities,id'],
            'entity_field_id' => ['required', 'exists:entity_fields,id'],
        ];
    }
}
