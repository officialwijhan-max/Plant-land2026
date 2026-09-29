<?php

namespace Modules\ProAccount\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ValidationMessage;

class CreatePaymentVoucherRequest extends FormRequest
{
    use ValidationMessage;
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            "date" => "required",
            "debit_account_id"  => "required|integer|min:0",
            "sub_amount.*"  => "required|regex:/^\d+(\.\d{1,2})?$/|min:0",
            "credit_sub_account_id"  => "required|integer|min:1",
            "cash_flow_account.*"  => "nullable"
            // "cash_flow_account.*"  => "required|integer|min:0"
        ];
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
