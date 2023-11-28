<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateShopRequest extends FormRequest
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

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => $validator->errors()->first(),
            'data'    => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {    
        return [
            'name' => 'sometimes',
            'email' => 'sometimes',
            'phone_number' => 'sometimes',
            'address' => 'sometimes',
            'open_close_time' => 'sometimes',
            'open_close_date' => 'sometimes',
            'latitude' => 'sometimes',
            'longitude' => 'sometimes',
            'image' => 'sometimes',
        ];
    }
    
    
    
}
