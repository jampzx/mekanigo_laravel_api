<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class CreateAppointmentRequest extends FormRequest
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
            'shop_id'=>'required',
            'service'=>'required',
            'name'=>'required',
            'contact_number'=>'required',
            'email'=>'required',
            'address'=>'required',
            'type'=>'required',
            // 'shop_latitude'=>'required',
            // 'shop_longitude'=>'required',
            'date'=>'required',
            // 'day'=>'required',
            'time'=>'required',
        ];
    }
}
