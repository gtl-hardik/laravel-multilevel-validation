<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\AccessToken;
use App\UserRefreshToken;
use DB;
use Carbon\Carbon;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT;
class RegisterController extends BaseController
{
    /**

     * Register api

     *

     * @return \Illuminate\Http\Response

     */

    public function register(Request $register)
    {
        $validator = Validator::make($register->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $input = $register->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken;
        $success['name'] =  $user->name;
        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    protected function refreshToken(Request $request)
    {
        $request->request->add([
            'grant_type' => 'refresh_token',
            'refresh_token' => $request->refresh_token,
            'client_id' => 2,
            'client_secret' => 'VpJYwIirciKs6a0PTxvAsfZxkdhOJijkyZ2Lk1hH',
            'scope' => ''
        ]);

        $proxy = Request::create(
            'oauth/token',
            'POST'
        );

        return \Route::dispatch($proxy);
    }

    public function userRefreshToken(Request $request)
    {
        $jwt = trim(preg_replace('/^(?:\s+)?Bearer\s/', '', $request->header('authorization')));
        $token = (new \Lcobucci\JWT\Parser())
            ->parse((string) $request->get('token'));
        // $token = (new \Lcobucci\JWT\Parser())->parse($jwt);
        $access_token = $token->getHeader('jti');
        $expires_at   = $token->getClaim('exp');
        // Get UserID
        $userToken = AccessToken::where('id', $access_token)->first();
        $userID = $userToken->users->id;

        // Get Refresh Token
        $refreshToken = $request->get('refresh_token');

        // Check Refresh Token
        $table = DB::table('oauth_refresh_tokens')
            ->select('id', 'expires_at')
            ->where('access_token_id', $access_token)
            ->where('revoked', 0)
            ->first();

        $expires_at = Carbon::parse($table->expires_at)->timestamp;

        // Check Refresh Token Expired
        if ($expires_at > Carbon::now()->timestamp) {
            // Refresh Access Token
            $data = [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => 2,
                'client_secret' => 'VpJYwIirciKs6a0PTxvAsfZxkdhOJijkyZ2Lk1hH',
                'scope' => '',
            ];

            $request = Request::create('/oauth/token', 'POST', $data);
            $response = app()->handle($request);
            $rData = json_decode($response->getContent());

            $data = array(
                "token_type" => $rData->token_type,
                "expires_in" => $rData->expires_in,
                "access_token" => $rData->access_token,
                "refresh_token" => $rData->refresh_token
            );

            return response()->json($data, 200);
        } else {
            // Refresh Token Expired
            // Get UX to show Login Modal
            return response()->json([
                'error' => [
                    'status' => 401,
                    'message' => 'Unauthorized',
                ],
            ], 401);
        }
    }
}
