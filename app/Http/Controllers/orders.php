<?php

namespace App\Http\Controllers;
use App\Models\order;
use App\Models\orderitem;
use App\Models\cart;
use App\Models\invoice;
use Illuminate\Http\Request;

class orders extends Controller
{
    public function index(){
        $order=order::all();
    }

    public function storeorder(Request $request){

        $request->validate([
        'table_id' => 'nullable|integer|exists:tables,id',
        // 'order_number' => 'required|string|max:50|unique:orders,order_number',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:255',
        'order_type' => 'required|in:dine_in,takeaway,delivery',
    ]);


        $order=new order();
        if($request->table_id){
            $order->table_id=$request->table_id;
        }
        else{
            $order->table_id=0;
        }
        if(order::exists()){
            $order_number=order::orderBy('order_number','desc')->first();
            $order->order_number=$order_number->order_number +1;
        }
        else{
            $order->order_number=1;
        }
        $order->phone=$request->phone;
        $order->address=$request->address;
        $order->order_type=$request->order_type;
        //$order->status=$request->status;
        // user-totalprice
        $order->save();

        $orderincart=cart::with('product')->where('user_id',auth::user()->id())->get();
        foreach($orderincart as $orders){
            $order_item=new order_item();
            $order_item->product_id=$orders->product_id;
            $order_item->order_id=$order->id;
            $order_item->price=$orders->product->price;
            $order_item->quantity=$orders->quantity;
            $order_item->sup_total=$orders->product->price * $orders->quantity;
            $order_item->save(); 
        }

        $total = OrderItem::where('order_id', $order->id)->sum('sup_total');
        $order->total_price = $total;
        $order->save();


        $invoice_number='INV_' . Date('ymd') . '_' . str_pad((order::max('id')+1) , 5 , 0 ,STR_PAD_LEFT);

        $invoice=new invoice();
        $invoice->order_id=$order->id;
        $invoice->invoice_number=$invoice_number;
        if($order->table_id){
            $invoice->table_number=$order->table_id;
        }
        else{
            $invoice->table_number=0;
        }
        $invoice->total_price=$order->total_price;
        $invoice->tax=$request->tax ??0;
        $invoice->discount=$request->discount ??0;
        $invoice->save();

        $invoice->final_price=$order->total_price + $invoice->tax - $invoice->discount;
        $invoice->save();
        cart::where('user_id',auth::user()->id())->delete();




    }
}
