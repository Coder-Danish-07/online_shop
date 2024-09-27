<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;

class OrderController extends Controller
{
    public function index(Request $request){

        $orders = Order::latest('orders.created_at')->select('orders.*','users.name','users.email');
        $orders = $orders->leftjoin('users','users.id','orders.user_id');

        if($request->get('keyword') != ""){
            $order = $orders->where('users.name','like','%'.$request->keyword.'%');
            $order = $orders->orWhere('users.email','like','%'.$request->keyword.'%');
            $order = $orders->orWhere('orders.id','like','%'.$request->keyword.'%');
        }

        $orders = $orders->paginate(10);
        // dd($orders);
        $data['orders'] = $orders;
        return view('admin.orders.index',$data);
    }

    public function detail($orderId){

        $order = Order::select('orders.*','countries.name as countryName')
                       ->where('orders.id',$orderId)
                       ->leftjoin('countries','countries.id','orders.country_id')
                       ->first();
        $data['order'] = $order;

        $orderItem = OrderItem::where('order_id',$orderId)->get();
        $data['orderItem'] = $orderItem;

        return view('admin.orders.detail',$data);
    }
}
