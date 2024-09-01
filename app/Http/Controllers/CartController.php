<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Gloudemans\Shoppingcart\Facades\Cart;

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
            $message = $product->title.' added in cart';

            }else{
                $status = false;
                $message = $product->title.' Already added in cart';
            }
        }
        else{
            // empty in cart 
            Cart::add($product->id, $product->title, 1, $product->price,['productImage' => (!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title.' added in cart';
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
}
