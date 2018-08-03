<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Contracts\Validation\Validator;
use App\Traits\APIResponse;
class Address extends FormRequest
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

    use CommonRules,APIResponse;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $filter = $this->addressRules();
        unset($filter['company_id']);
        return array_merge($filter, [
            'company'    => 'required',
            'entity_id'  => 'required|in:1,2,3,4,5,6',
            'email'      => 'required|email',
            'tel'        => 'required',
            'website'    => 'url',
        ]);
    }

    /**
     * [failedValidation [Overriding the event validator for custom error response]]
     * @param  Validator $validator [description]
     * @return [object][object of various validation errors]
     */
    public function failedValidation(Validator $validator) {
        $this->sendAPIValidationError('validation error',$validator->errors());
    }
}
