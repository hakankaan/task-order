<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class CreateTaskRequest extends FormRequest
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
            'title' => ['required', 'max:255'],
            'type' => ['required', Rule::in(['invoice_ops', 'custom_ops', 'common_ops'])],
            'amount.quantity' => ['required_if:type,invoice_ops', 'integer', 'prohibited_unless:type,invoice_ops'],
            'amount.currency' => ['required_if:type,invoice_ops', Rule::in(['€', '₺', '$', '£']), 'prohibited_unless:type,invoice_ops'],
            'country' => ['required_if:type,custom_ops', 'max:40', 'prohibited_unless:type,custom_ops'],
        ];
    }

    public function messages()
    {
        return [
            'amount.quantity.required' => 'type: invoice_ops için amount.quantity zorunludur.',
            'amount.currency.required' => 'type: invoice_ops için amount.currency zorunludur.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(["result" => "failure", "errors" => $validator->errors()->all()], 422));
    }
}
