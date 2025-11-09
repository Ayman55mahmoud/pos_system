<?php

namespace App\Http\Controllers;
use App\Models\product;
use App\Models\image;
use Illuminate\Http\Request;

class products extends Controller
{
    public function index(){
        $product=product::all();
        return view('product.show',compact('product'));
    }
    
    public function storeproduct(Request $request){

        $request->validate([
            'name'=>'required',
            'price'=>'required',
            'quantity'=>'required',
            'category_id'=>'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);



        $product=new product();
        $product->name=$request->name;
        $product->price=$request->price;
        $product->quantity=$request->quantity;
        $product->category_id=$request->category_id;
        if($request->has('image')){
            $nameimage=str::uuid()->tostring() .'.'. $request->image->getClientOriginalExtension();
             $pathimage=$request->image->move('upload',$nameimage);
             $product->image=$pathimage;
        }
    }

    public function deleteprduct($id){
        $delpro=product::find($id);
        $delpro->delete();
    }

    public function editproduct($id){
        $product=product::find($id);
        return view('product.edit',compact('product'));
    }

    public function updateproduct(Request $request){

         $request->validate([
            'name'=>'required',
            'price'=>'required',
            'quantity'=>'required',
            'category_id'=>'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);
        
        $newporduct=product::where('id',$request->id)->first();
        $newporduct->name=$request->name;
        $newporduct->price=$request->price;
        $newporduct->quantity=$request->quantity;
        $newporduct->category_id=$request->category_id;
        if($request->has('image')){
            $nameimage=str::uuid()->tostring() .'.'. $request->image->getClientOriginalExtension();
             $pathimage=$request->image->move('upload',$nameimage);
             $product->image=$pathimage;
        }
       

    }


     public function addproductIMAGE(Request $request ){
        $img= new image();
        $img->product_id=$request->id;
         if($request->hasFile('photo')){
        $nameimg=str::uuid()->tostring() . '.' . $request->photo->getClientOriginalExtension();
        $pathpoto=$request->photo->move('uploud',$nameimg );
        $img->img=$pathpoto;
        }
        $img->save();
      
    }




}
