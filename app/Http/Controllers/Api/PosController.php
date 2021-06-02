<?php

namespace App\Http\Controllers\Api;

use App\Warranty;
use Illuminate\Http\Request;


class PosController extends Controller{


public function login(){

    try{
    $validation = validator()->make($request->all(), [
                    'email' => 'required|email|exists:users,email',
                    'password' => 'required',
                ]);
                if($validation->fails())
                {
                    return response()->json($validation->messages(), 401);
                }

                $user = User::where('email', $request->email)->first();
                // return $this->apiResponse('error', $user);

                if ($user)
                {
                    if(Hash::check($request->password, $user->password)){
                        return  response()->json([
                            'api_token' => $user->api_token,
                        ],200);
                    }else {
                        return response()->json(['error' => 'Invalid access credentials'],401);
                    }
                }else {
                    return response()->json(['error'=>'Invalid access credentials'],401);
                }

        } catch (\Exception $e) {
        return response()->json([$e->getMessage()], 401);
        }
    }
}
