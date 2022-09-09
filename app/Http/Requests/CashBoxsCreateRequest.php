<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CashBoxsCreateRequest extends FormRequest
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
            'plantel_id'=>"required",
            'fecha'=>'required',
            'customer'=>'required',
            'total'=>'required'
        ];
    }

    public function messages()
    {
        return [
            'plantel_id.required'=>"Campo obligatorio",
            'fecha.required'=>'Campo obligatorio',
            'customer.required'=>'Campo obligatorio',
            'total.required'=>'Campo obligatorio'
        ];
    }
}
