<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function login(Request $request){
    	$credentials=$request->only('username','password');
    	try{
    		if (! $token=JWTAuth::attempt($credentials)){
    			return response()->json(['error'=>'invalid_credentials'], 400);
    		}
    	} catch (JWTException $e){
    		return rseponse()->json(['error'=>'could_not_create_token'], 500);
    	}
    	return response()->json(compact('token'));
    }
    public function register(Request $request){
    	$validator=Validator::make($request->all(),[
    		'username'=>'required|string|max:255|unique:users',
    		'password'=>'required|integer|digits:4',
    		'jml_saldo'=>'integer',
    	]);
    if($validator->fails()){
    	return response()->json($validator->errors()->toJson(), 400);
    }
    $user=User::create([
    	'username'=>$request->get('username'),
    	'password'=>Hash::make($request->get('password')),
        'jml_saldo' => $request->get('jml_saldo') ,
    ]);
		$token=JWTAuth::fromUser($user);
		return response()->json(compact('user','token'),201);
	}
	public function getAuthenticatedUser(){
		try{
			if (! $user=JWTAuth::parseToken()->authenticate()){
				return response()->json(['user_not_found'],404);
			}
		} catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e){
			return response()->json(['token_expired'],$e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e){
			return response()->json(['token_invalid'],$e->getStatusCode());
		} catch (Tymon\JWTAuth\Exceptions\JWTException $e){
			return response()->json(['token_absent'],$e->getStatusCode());
		}
		return response()->json(compact('user'));
	}
    public function updateSaldo(Request $request){
        // try{
            $user=User::where('username',$request->username)->first();
            $user -> jml_saldo =$user -> jml_saldo + $request -> jml_saldo;
            $user->save();
            return response()->json([
            'Update Saldo Berhasil',
            $user,
        ]);
        // }catch(\Exception $e){
        // return response()->json([
        //     'Update Saldo Gagal',
        // ]);
    }
}
