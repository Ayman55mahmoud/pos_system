<?php

namespace App\Http\Controllers;
use App\Models\cart;
use Illuminate\Http\Request;

class carts extends Controller
{
    public function index(){
        $carts=cart::with('product')->where('user_id',)->all;

        return view('cart',compact('carts'));
    }

    public function storecart($id){

        $cart=cart::where('user_id',)->where('product_id',$id)->get();
        if($cart){
            $cart->quantity +=1;
            $cart->save();
        }
        else{
            $newcart=new cart();
            $newcart->product_id=$id;
            $newcart->quantity=1;
            //user_id
            $newcart->save();
        }
    }


    public function deletecart($id){
        cart::find($id)->dalete();
    
    }



    
}
