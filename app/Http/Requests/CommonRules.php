<?php
namespace App\Http\Requests;

trait CommonRules
{

    protected function authRules()
    {
        return [
            'name'          => 'required',
            'email'         => 'required|email|unique:users',
            'password'      => 'required',
            'c_password'    => 'required|same:password',
        ];
    }

    protected function addressRules()
    {
        return [
            'company_id'      => 'required|numeric',
            'address_line_1'  => 'required|min:3|max:80',
            'address_line_2'  => 'min:3|max:80',
            'address_line_3'  => 'min:3|max:80',
            'area_town'       => 'required|alpha|min:3',
            'county'          => 'required|min:3|max:80',
            'postcode'        => 'required',
        ];
    }
}