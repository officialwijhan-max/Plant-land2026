<?php

namespace Modules\ProAccount\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ValidationMessage;

class CreateLeadgerRequest extends FormRequest
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
            "code" => ['required', 'max:11', 'unique:pro_leadgers'],
            "type" => "required",
            "is_active" => "required",
            'is_cost_center' => 'required',
            'description' => 'nullable',
            'as_sub_category' => 'nullable'
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
