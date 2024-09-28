<?php
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Order;
use App\Mail\OrderEmail;
use Illuminate\Support\Facades\Mail;
use App\Models\Country;

function getCateories(){
    return Category::orderBy('name','ASC')
           ->with('sub_category')
           ->where('status',1)
           ->where('showHome','Yes')
        //    ->orderBy('id','DESC')
           ->get();
}

function ProductImage($product_id){
   return ProductImage::where('product_id',$product_id)->first();
}

function OrderEmail($orderId){
   $order = Order::where('id',$orderId)->with('items')->first();

   $mailData = [
      'subject' => 'Thanks for your order',
      'order'   => $order,
   ];

   Mail::to($order->email)->send(new OrderEmail($mailData));
   // dd($order);
}

function getCountry($id){
   return Country::where('id',$id)->first();
}
?>