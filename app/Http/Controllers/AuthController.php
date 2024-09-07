<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    public function login(){
        return view("front.account.login");
    }

    public function register(){
        return view('front.account.register');
    }

    public function processRegister(Request $request){

        $validaor = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:5|confirmed'
        ]);

        if($validaor->passes()){

            $user = new User;
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();

            session()->flash('success','you have been registered successfully');
            return response()->json([
                'status' => true,
                'message'=> 'you have been registered successfully'
            ]);

        }
        else{

            return response()->json([
                'status' => false,
                'errors' => $validaor->errors()
            ]);
        }
    }
}
