<?php

namespace App\Http\Controllers;
use App\Models\category;
use Illuminate\Http\Request;

class Categorys extends Controller
{

    public function index()
    {
        $category=category::all();
        return view('category.show',compact('category'));
    }

    public function storecategory(Request $request){

         $request->validate([
            'name'=>'required',
            'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $category=new category();
        $category->name=$request->name;
        if($request->has('image')){
             $nameimage=str::uuid()->tostring() .'.'. $request->image->getClientOriginalExtension();
             $pathimage=$request->image->move('upload',$nameimage);
             $category->image=$pathimage;
        }
        $category->save();

    }

    public function deletecategory($id){
        $del=category::find($id);
        $del->delete();
    }

    public function editcategory($id){
        $category=category::find($id);
        return view('category.edit');

    }
    
    public function updatecategory(Request $request){
        $request->validate([
            'name'=>'required',
             'image' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        $category=category::where('id',$request->id)->first();
        $category->name=$request->name;

        if($request->has('image')){
             $nameimage=str::uuid()->tostring() .'.'. $request->image->getClientOriginalExtension();
             $pathimage=$request->image->move('upload',$nameimage);
             $category->image=$pathimage;
        }
       
        $category->save();

    }



   
    
}
