<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DiscountCoupon;
use Illuminate\Support\Carbon;
class DiscountCodeController extends Controller
{
    public function index(){
        return view('admin.coupon.index');
    }

    public function create(){
        return view('admin.coupon.create');
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(),[

            'code' => 'required',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'status' => 'required',

        ]);

        if($validator->passes()){

            //Starting Date Must be greater then current date
            if(!empty($request->starts_at)){
                $now = Carbon::now();

                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if($startAt->lte($now) == true){
                    return response()->json([
                        'status' =>false,
                        'errors' => ['starts_at' => 'Start date can not be less then curren date time'],
                    ]);
                }
            }

            //expire date must be greater then start date
            if(!empty($request->starts_at) && !empty($request->expires_at)){

                $expiresAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->expires_at);

                $startAt = Carbon::createFromFormat('Y-m-d H:i:s',$request->starts_at);

                if($expiresAt->gt($startAt) == false){
                    return response()->json([
                        'status' =>false,
                        'errors' => ['expires_at' => 'Expiry date must be greater then Start date'],
                    ]);
                }
            }

            $DiscountCode = new DiscountCoupon();
            $DiscountCode->code = $request->code;
            $DiscountCode->name = $request->name;
            $DiscountCode->description = $request->description;
            $DiscountCode->max_uses = $request->max_uses;
            $DiscountCode->max_uses_user = $request->max_uses_user;
            $DiscountCode->type = $request->type;
            $DiscountCode->discount_amount = $request->discount_amount;
            $DiscountCode->min_amount = $request->min_amount;
            $DiscountCode->status = $request->status;
            $DiscountCode->starts_at = $request->starts_at;
            $DiscountCode->expires_at = $request->expires_at;
            $DiscountCode->save();

            $message = 'Coupon Code added Successfully';
            session()->flash('success',$message);

            return response()->json([
                'status' =>true,
                'message' => $message,
            ]);

        }
        else{
            return response()->json([
                'status' =>false,
                'errors' => $validator->errors(),
            ]);
        }

    }

    public function edit(){
        
    }

    public function update(){
        
    }

    public function destroy(){
        
    }
}
