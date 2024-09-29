<?php

namespace App\Http\Controllers;

use App\Models\CustomerAddress;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Wishlist;

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

        $userId = Auth::user()->id;
        $user = User::where('id',$userId)->first();
        $address = CustomerAddress::where('user_id',$userId)->first();
        $countries = Country::orderBy('name','ASC')->get();
        $data['user'] = $user;
        $data['address'] = $address;
        $data['countries'] = $countries;

        return view('front.account.profile',$data);
    }

    public function updateProfile(Request $request){
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userId.',id',
            'phone' => 'required',
        ]);

        if($validator->passes()){
            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();

            session()->flash('success','Profile Updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Profile Updated Successfully',
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
    
    public function updateAddress(Request $request){
        
        $userId = Auth::user()->id;
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:3',
            'last_name' => 'required',
            'email' => 'required|email',
            'country_id' => 'required',
            'address' => 'required|min:20',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',            
        ]);

        if($validator->passes()){

            CustomerAddress::updateOrCreate(
                ['user_id' => $userId],
                [
                    'user_id' => $userId,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country_id,
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip,
                ]
            );

            session()->flash('success','Address Updated Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Address Updated Successfully',
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
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
        $orderItemsCount = OrderItem::where('order_id',$id)->count();

        $data['orderItemsCount'] = $orderItemsCount;
        $data['orderItems'] = $orderItems;
        $data['order'] = $order;

        return view('front.account.order_detail',$data);

    }

    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')
        ->with('success', 'You successfully logged out');
    }

    public  function wishlist(){
        
        $wishlists = Wishlist::where('user_id',Auth::user()->id)->with('product')->get();
        $data['wishlists'] = $wishlists;
        return view('front.account.wishlist',$data);
    }

    public function removeWishlist(Request $request){

        $wishlist = Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->first();
        
        if($wishlist == null){
         
            $message = 'Product is already remove in wishlist.';
            session()->flash('error',$message);
            return response()->json([
                'status' => true,
                'message' => $message
            ]);

        }

        Wishlist::where('user_id',Auth::user()->id)->where('product_id',$request->id)->delete();
        session()->flash('success','Product Remove in wishlist successfully');
        return response()->json([
            'status' => true,
            'message' => 'Product Remove in wishlist successfully',
        ]);
        
    }
}
