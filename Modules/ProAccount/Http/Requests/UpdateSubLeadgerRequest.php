<?php

namespace Modules\ProAccount\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ValidationMessage;

class UpdateSubLeadgerRequest extends FormRequest
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
            "code" => ['required', 'max:51'],
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
