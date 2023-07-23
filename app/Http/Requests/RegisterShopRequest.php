<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterShopRequest extends FormRequest
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
            'name'=>'required|min:3',
            'email'=>'required|email|unique:users',
            'phone_number'=>'required|unique:users',
            'address'=>'required|min:5',
            'user_type'=>'required',
            'open_close_time'=>'required|min:5',
            'open_close_date'=>'required|min:5',
            'latitude'=>'required',
            'longitude'=>'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'password'=>'required|min:8',
        ];
    }
}
