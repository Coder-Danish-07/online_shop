<?php
use App\Models\Category;
use App\Models\ProductImage;
use App\Models\Order;
use App\Models\Page;
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

function OrderEmail($orderId,$userType="customer"){
   $order = Order::where('id',$orderId)->with('items')->first();
  
   if($userType == 'customer'){
      $subject = 'Thanks for your order';
      $email = $order->email;
   }
   else{
      $subject = 'You have recieved an order.';
      $email = env('ADMIN_EMAIL');
   }
  
   $mailData = [
      'userType' => $userType,
      'subject' => $subject,
      'order'   => $order,
   ];

   Mail::to($email)->send(new OrderEmail($mailData));
   // dd($order);
}

function getCountry($id){
   return Country::where('id',$id)->first();
}

function staticPage(){
   $pages = Page::orderBy('name','ASC')->get();
   return $pages;
}
?>