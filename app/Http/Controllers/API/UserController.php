<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use App\Http\Requests\Address;
use App\Traits\APIResponse;
class UserController extends Controller
{

    use APIResponse;

    public function createAddress(Address $request){
        $input = $request->all();
        return $this->sendResponse($input, 'Address stored successfully.');
    }

}
