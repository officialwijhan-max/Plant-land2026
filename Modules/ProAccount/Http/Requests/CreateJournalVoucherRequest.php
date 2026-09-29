<?php

namespace Modules\ProAccount\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ValidationMessage;

class CreateJournalVoucherRequest extends FormRequest
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
            "is_cashflow_journal" => "required",
            "account_id.*"  => "required|gt:0",
            "debit_account_id.*"  => "required",
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
