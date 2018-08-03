<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Auth extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    use CommonRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge($this->addressRules(), [
            'company'         => 'required',
            'entity_id'       => 'required|in:1,2,3,4,5,6',
            'email'           => 'required|email',
            'tel'             => 'required',
            'website'         => 'url',
        ]);
    }
}
