<?php

namespace Modules\ExtraUser\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtraUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            "contact_type" => "required",
            "name" => "required",
            "email" => "nullable",
            "business_name" => "nullable",
            "contact_id" => "nullable",
            "tax_number" => "nullable",
            "opening_balance" => "sometimes|nullable|numeric",
            "pay_term" => "nullable",
            "pay_term_condition" => "sometimes|nullable|string",
            "customer_group" => "nullable",
            "credit_limit" => "sometimes|nullable|numeric",
            "alternate_contact_no" => "sometimes|nullable|string",
            "country_id" => "sometimes|nullable|integer",
            "state_id" => "sometimes|nullable|integer",
            "city_id" => "sometimes|nullable|integer",
            "address" => "sometimes|nullable|string",
            "note" => "sometimes|nullable|string",
            "mobile" => "sometimes|nullable|string",
        ];

        return $rules;
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
