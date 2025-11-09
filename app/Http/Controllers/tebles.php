<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class tebles extends Controller
{
    public function index(){
        $teble=teble::all();
        return view('table.show',compact('teble'));
    }

    public function storetable(Request $request){
        $teble=new table();
        $teble->number_table=$request->number_table;
        $teble->stetus=$request->stetus;
    }
}
