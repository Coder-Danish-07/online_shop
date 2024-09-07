<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Contracts\Session\Session;

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

    
}
