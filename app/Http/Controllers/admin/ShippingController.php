<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;
use App\Models\ShippingCharge;

class ShippingController extends Controller
{
    public function create(){

        $countries = Country::get();
        $data['countries'] = $countries;

        $shippingCharges = ShippingCharge::select('shipping_charges.*','countries.name')
                          ->leftjoin('countries','countries.id','shipping_charges.country_id')->get();

        $data['shippingCharges'] = $shippingCharges;

        return view('admin.shipping.create',$data);
    }

    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if($validator->passes()){

            $count = ShippingCharge::where('country_id',$request->country)->count();
            if($count > 0){
                session()->flash('error','Shipping Already Added');
                return response()->json([
                    'status' => true,
                ]);
            }

            $shipping = new ShippingCharge;
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
            
            session()->flash('success','Shipping Added Successfully');

            return response()->json([
                'status' => true,
                'message' => 'Shipping Added Successfully',
            ]);

        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function edit($id, Request $request){

        $countries = Country::get();
        $data['countries'] = $countries;

        $shippingCharge = ShippingCharge::find($id);
        $data['shippingCharge'] = $shippingCharge;


        return view('admin.shipping.edit',$data);
    }

    public function update($id,Request $request){

        $shipping = ShippingCharge::find($id);

        $validator = Validator::make($request->all(), [
            'country' => 'required',
            'amount' => 'required|numeric'
        ]);

        if($validator->passes()){

            if($shipping == null){
                session()->flash('error','Shipping Not Found');
                return response()->json([
                    'status' => true,
                    'message'=> 'Shipping Not Found'
                ]);
            }
            
            $shipping = ShippingCharge::find($id);
            $shipping->country_id = $request->country;
            $shipping->amount = $request->amount;
            $shipping->save();
            
            session()->flash('success','Shipping Update Successfully');
            return response()->json([
                'status' => true,
                'message' => 'Shipping Update Successfully',
            ]);

        }
        else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function destroy($id){

        $shipping = ShippingCharge::find($id);

        if($shipping == null){
            session()->flash('error','Shipping Not Found');
            return response()->json([
                'status' => true,
                'message'=> 'Shipping Not Found'
            ]);
        }

        $shipping->delete();
        session()->flash('success','Shipping Deleted successfully');
        return response()->json([
            'status' => true,
            'message'=> 'Shipping Deleted successfully'
        ]);

    }
}
