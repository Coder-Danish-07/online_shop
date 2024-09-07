<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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

    public function authienticate(Request $request){

        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->passes()){

            if(Auth::attempt(['email'=> $request->email,'password'=> $request->password],$request->get('remember'))){

                return redirect()->route("account.profile");

            }
            else{
                return redirect()->route("account.login")
                ->withInput($request->only('email'))
                ->with('error', 'Either Email/Password is incorrect');
            }

        }
        else{

            return redirect()->route("account.login")
            ->withErrors($validator)
            ->withInput($request->only('email'));

        }

    }

    public function profile(){
        return view('front.account.profile');
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')
        ->with('success', 'You successfully logged out');
    }
}
