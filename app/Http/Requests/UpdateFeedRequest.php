<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFeedRequest extends FormRequest
{
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
            'title'=>'required|min:6',
            'date'=>'required|min:6',
            'disasterType'=>'required|min:6',
            'location'=>'required|min:6',
            'information'=>'required|min:6',

            //moving this rule to front end as image has issue/limitation in php PUT and PATCH method
            //'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ];
    }
}
