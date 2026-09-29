<?php

namespace Modules\ProAccount\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ValidationMessage;

class CashFLowRequest extends FormRequest
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
            "name" => "required",
            "code" => 'required|unique:pro_cash_flow_accounts,code,'.$this->id,
            "type" => "required",
            "is_active" => "required",
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
