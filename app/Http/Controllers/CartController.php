<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingCharge;
use Illuminate\Support\Carbon;

class CartController extends Controller
{
    public function addToCart(Request $request){

        $product = Product::with('product_images')->find($request->id);

        if($product == null){
            return response()->json([
                'status' =>false,
                'message' => 'Product Not Found'
            ]);
        }

        if(Cart::count() > 0){
            //already addeed a product in cart
            //Product found in cart
            //check if this product already in cart
            //return a message  that product already added in cart 
            //if product not found in the cart, then add product in cart

            $cartContent = Cart::content();
            $productAlreadyExist = false;

            foreach($cartContent as $item){
                if($item->id == $product->id){
                    $productAlreadyExist = true;
                }
            }

            if($productAlreadyExist == false){
            Cart::add($product->id, $product->title, 1, $product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);

            $status = true;
            $message = '<strong>'.$product->title.'</strong> added in your cart successfully';
            Session()->flash('success',$message);

            }else{
                $status = false;
                $message = $product->title.' Already added in cart';
            }
        }
        else{
            // empty in cart 
            Cart::add($product->id, $product->title, 1, $product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = '<strong>'.$product->title.'</strong> added in your cart successfully';
            Session()->flash('success',$message);
        }

        return response()->json([
           'status' => $status,
           'message' => $message, 
        ]);

    }

    public function cart(){
        $cartContent = Cart::content();
        // dd($cartContent);
        $data['cartContent'] = $cartContent;
        return view('front.cart', $data);
    }

    public function updateCart(Request $request){
        $rowId = $request->rowId;
        $Qty = $request->Qty;

        //trackQty
        $itemInfo = Cart::get($rowId);
        $product = Product::find($itemInfo->id);
        if($product->track_qty == "Yes"){
            if($Qty <= $product->qty){
                Cart::update( $rowId, $Qty );
                $message = 'Cart Updated Successfully';
                $status = true;
                session()->flash('success', $message);
            }
            else{
                $message = 'Requested qty('.$Qty.') not available in stock';
                $status = false;
                session()->flash('error', $message);
            }
        }
        else{
            Cart::update( $rowId, $Qty );
            $message = 'Cart Updated Successfully';
            $status = true;
            session()->flash('success', $message);
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);

    }

    public function deleteItem(Request $request){

        $itemInfo = Cart::get($request->rowId);

        if($itemInfo == null){

            Session()->flash('error','Item not found');
            return response()->json([
                'status'=> false,
                'message'=> 'Item not found'
            ]);
        }

        Cart::remove($request->rowId);
        Session()->flash('success','Item Deleted Successfully');
        return response()->json([
            'status'=> true,
            'message'=> 'Item Deleted Successfully'
            ]);

    }

    public function checkout(){

        $discount = 0;
        //cart is empty to redirect cart page
        if(Cart::count() == 0){
            return redirect()->route('front.cart');
        }

        // not a login user to redirect login page
        if(Auth::check() == false){

            if(!session()->has('url.intended')){
                session(['url.intended' => url()->current()]);
            }
            return redirect()->route('account.login');
        }

        session()->forget('url.intended');

        $customerAddress = CustomerAddress::where('user_id',Auth::user()->id)->first();

        $countries = Country::orderBy('name','ASC')->get();
        $subtotal = Cart::subtotal(2,'.','');
         //Apply discount here
         if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subtotal;
            }
            else{
                $discount = $code->discount_amount;
            }
        }

        //Shipping  calculate
        if($customerAddress != ''){
        $userCountry = $customerAddress->country_id;
        $shippingInfo = ShippingCharge::where('country_id',$userCountry)->first();

        $totalQty = 0;
        $totalShippingCharge = 0;
        $grandTotal = 0;
        foreach(Cart::content() as $item){
            $totalQty += $item->qty;
        }
        if($shippingInfo != null){
            $totalShippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = ($subtotal-$discount)+$totalShippingCharge;
        }else{
            $shippingInfo = ShippingCharge::where('country_id','rest_of_world')->first();
            $totalShippingCharge = $totalQty*$shippingInfo->amount;
            $grandTotal = ($subtotal-$discount)+$totalShippingCharge;
        }
        }
        else{
            $totalShippingCharge = 0;
            $grandTotal = ($subtotal-$discount)+$totalShippingCharge;
        }
        return view('front.account.checkout',[
            'countries' => $countries,
            'customerAddress' => $customerAddress,
            'totalShippingCharge' => $totalShippingCharge,
            'discount' => $discount,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function processCheckout(Request $request){

        //Step 1 is validate the customer data
        $validator = Validator::make($request->all(),[
            'first_name' => 'required|min:3',
            'last_name' => 'required',
            'email' => 'required|email',
            'country' => 'required',
            'address' => 'required|min:20',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required',
            'mobile' => 'required',            
        ]);

        if($validator->fails()){
            
            return response()->json([
                'message' => 'Please fix this errors',
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        //Step 2 Store the customer data in customer addresses table
        $user = Auth::user();

        CustomerAddress::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->apartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]
        );

        //Step 3 Store the data in orders table

        if($request->payment_method == 'cod'){

            $discount = 0;
            $subTotal = Cart::subtotal(2,'.','');
            $promoCode = '';
            $discountCodeId = Null;

            //Apply discount here
            if(session()->has('code')){
                $code = session()->get('code');
                if($code->type == 'percent'){
                    $discount = ($code->discount_amount/100)*$subTotal;
                }
                else{
                    $discount = $code->discount_amount;
                }
                $promoCode = $code->code;
                $discountCodeId = $code->id;
            }

            $shippingInfo = ShippingCharge::where('country_id', $request->country)->first();
            $totalQty = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }
            if($shippingInfo != null){

                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shipping;
                
            }else{

                $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
                $shipping = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subTotal-$discount)+$shipping;

            }

            $order = new Order;

            $order->user_id = $user->id;
            $order->subtotal = $subTotal;
            $order->shipping = $shipping;
            $order->grand_total = $grandTotal;
            $order->discount = $discount;
            $order->coupon_code = $promoCode;
            $order->coupon_code_id = $discountCodeId;
            $order->payment_status = 'not paid';
            $order->status = 'pending';
            $order->first_name = $request->first_name;
            $order->last_name = $request->last_name;
            $order->email = $request->email;
            $order->mobile = $request->mobile;
            $order->country_id = $request->country;
            $order->address = $request->address;
            $order->apartment = $request->apartment;
            $order->city = $request->city;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->notes = $request->order_notes;
            $order->save();

            //Step 4 Store order items in order items table

            foreach(Cart::content() as $item){
                
                $orderItem = new OrderItem;
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item->id;
                $orderItem->name = $item->name;
                $orderItem->qty = $item->qty;
                $orderItem->price = $item->price;
                $orderItem->total = $item->price*$item->qty;
                $orderItem->save();

            }

            //Send Order Email
            OrderEmail($order->id);
            
            Cart::destroy();
            session()->forget('code');
            session()->flash('success','you have successfully placed in your order');
            return response()->json([
                'status' => true,
                'orderId' => $order->id,
                'message'=> 'you have successfully placed in your order'
            ]);

        }
        else{

        }



    }

    public function thankyou($id){

        return view('front.thanks',[
            'id' => $id,
        ]);
    }

    public function getOrderSummery(Request $request){

        $subtotal = Cart::subtotal(2,'.','');
        $discount = 0;
        $discountString = '';
        //Apply discount here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subtotal;
            }
            else{
                $discount = $code->discount_amount;
            }
            $discountString = '<div class="mt-4" id="discount-response">
                <strong>'.session()->get('code')->code.'</strong>
                <a class="btn btn-sm btn-danger" id="remove-discount"><i class="fa fa-times"></i></a>
                </div>'; 
        }

        if($request->country_id > 0){

            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();

            $totalQty = 0;
            foreach(Cart::content() as $item){
                $totalQty += $item->qty;
            }

            if($shippingInfo != null){

                $ShippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subtotal-$discount)+$ShippingCharge;

                return response()->json([
                    'status'=> true,
                    'ShippingCharge' => number_format($ShippingCharge,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'grandTotal' => number_format($grandTotal,2),
                ]);

            }else{

                $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
                $ShippingCharge = $totalQty*$shippingInfo->amount;
                $grandTotal = ($subtotal-$discount)+$ShippingCharge;

                return response()->json([
                    'status'=> true,
                    'ShippingCharge' => number_format($ShippingCharge,2),
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'grandTotal' => number_format($grandTotal,2),
                ]);
            }
        }
        else{

            // $grandTotal = Cart::subtotal(2,'.','');
            return response()->json([
                'status'=> true,
                'ShippingCharge' => number_format(0,2),
                'discount' => number_format($discount,2),
                'discountString' => $discountString,
                'grandTotal' => number_format(($subtotal-$discount),2),
            ]);
        }

    }

    public function applyDiscount(Request $request){
        // dd($request->all());
        $code = DiscountCoupon::where('code',$request->code)->first();
        
        if($code == null){

            return response()->json([
                'status' => false,
                'message' => 'Invalid Discount Coupon'
            ]);

        }

        //check if coupon start date is valid or not

        $now = Carbon::now();

        if($code->starts_at != ""){
            $startDate = Carbon::createFromFormat("Y-m-d H:i:s",$code->starts_at);

            if($now->lt($startDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Discount Coupon'
                ]);
            }
        }

        if($code->expires_at != ""){
            $endDate = Carbon::createFromFormat("Y-m-d H:i:s",$code->expires_at);

            if($now->gt($endDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid Discount Coupon'
                ]);
            }
        }

        //Max Uses check for coupon
        if($code->max_uses > 0){
            $couponUsed  = Order::where('coupon_code_id',$code->id)->count();

            if($couponUsed >= $code->max_uses){
                return response()->json([
                    'status' => false,
                    'message' => 'Maximum uses for coupon used next time try...'
                ]);
            }
        }

        //Max Uses For user coupon used check
        if($code->max_uses_user){
            $couponUsedByUser  = Order::where(['coupon_code_id' => $code->id,'user_id' => Auth::user()->id])->count();
            if($couponUsedByUser >= $code->max_uses_user){
                return response()->json([
                    'status' => false,
                    'message' => 'You have a already used in coupon for maximum times..'
                ]);
            }
        }

        //Min Amount Condition check
        $subtotal = Cart::subtotal(2,'.','');
        if($code->min_amount > 0){
            if($subtotal < $code->min_amount){
                return response()->json([
                    'status' => false,
                    'message' => 'Your min amount must be $'.$code->min_amount.'.',
                ]);
            }
        }
        
        session()->put('code',$code);

        return $this->getOrderSummery($request);

    }
    public function removeCoupon(Request $request){
        Session()->forget('code');
        return $this->getOrderSummery($request);
    }
}
