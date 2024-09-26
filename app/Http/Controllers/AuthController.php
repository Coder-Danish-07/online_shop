<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;

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

                if(session()->has('url.intended')){
                    return redirect(session()->get('url.intended'));
                }

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

    public function orders(){
        $user = Auth::user();
        $orders = Order::where('user_id',$user->id)->OrderBy('created_at','DESC')->get();
        $data['orders'] = $orders;

        return view('front.account.orders',$data);
    }

    public function orderDetail($id){
        $user = Auth::user();
        $order = Order::where('user_id',$user->id)->where('id',$id)->first();

        $orderItems = OrderItem::where('order_id',$id)->get();
        $data['orderItems'] = $orderItems;
        $data['order'] = $order;

        return view('front.account.order_detail',$data);

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')
        ->with('success', 'You successfully logged out');
    }
}
