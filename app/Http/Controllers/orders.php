<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\order;
use App\Models\product;
use App\Models\order_itme;
use App\Models\cart;
use App\Models\table;
use App\Models\invoice;
use Illuminate\Http\Request;

class orders extends Controller
{
    


     public function index(){
        $orders=order::with('table')->get();
        return view('pages.order.order',compact('orders'));
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



   public function editorder($id){
    $order = Order::with('items.product')->findOrFail($id);
    $products = Product::all(); 
    return view('pages.order.edit', compact('order', 'products'));
   }



    public function update(Request $request, $orderId)
  {
        $order = Order::findOrFail($orderId);

        
    // تعديل المنتجات 
        
        if($request->has('items')){
            foreach($request->items as $itemData){
                
                $item = order_itme::find($itemData['item_id']);

                if($item){
                    $item->product_id = $itemData['product_id'];
                    $item->quantity = $itemData['quantity'];
                    $item->sub_total = $item->product->price * $item->quantity;
                    $item->save();
                }
            }
        }

        
        //  إضافة منتجات 
        
        if($request->has('new_items')){
            foreach ($request->new_items as $newItem) {
        
        if (!isset($newItem['product_id'])) {
            continue;
        }

        
        $quantity = isset($newItem['quantity']) && is_numeric($newItem['quantity'])
                    ? (int) $newItem['quantity']
                    : 1;

        $product = Product::find($newItem['product_id']);
        if (!$product) {
            
            continue;
        }

        order_itme::create([
            'order_id'   => $orderId,
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'price'      => $product->price,
            'sub_total'  => $product->price * $quantity,
        ]);
    }

        }

        
        // 3 حذف المنتجات
        
        if($request->filled('deleted_items')) {
        $deleted = is_array($request->deleted_items)
                ? $request->deleted_items
                : explode(',', $request->deleted_items); // لو جاي ك string
                
        order_itme::whereIn('id', $deleted)->delete();
    }


        // ==============
        //  تحديث إجمالي الطلب
        // ==============
        $order->total_price = order_itme::where('order_id',$orderId)->sum('sub_total');
        $order->save();

        return redirect('/orders')->with('success', 'Order Updated Successfully!');
}


public function deleteorder($id){
    $del=order::findOrFail($id);
    $del->delete();

    return redirect('/orders');

}

public function create()
{
    
    $products = Product::all();

    return view('pages.order.create', compact('products'));
}


public function store(Request $request)
{

    $order_number='INV_' . Date('ymd') . '_' . str_pad((order::max('id')+1) , 5 , 0 ,STR_PAD_LEFT);
    // 1- إنشاء الطلب
    $order = Order::create([
        'table_id' => $request->table_number,
        'phone'        => $request->phone,
        'order_type'   => $request->order_type,
        'address'      => $request->address,
        'total_price'        => 0, 
        'order_number' => $order_number,
        'user_id' => Auth::id(),
    ]);


    $total = 0;

    // 2- إدخال كل المنتجات
    foreach ($request->items as $item) {
     
       
        if(!isset($item['product_id'], $item['quantity'])) continue;

        $product = Product::find($item['product_id']);
        $quantity = $item['quantity'];
        
        $subtotal = $product->price * $quantity;


        

        order_itme::create([
            'order_id'   => $order->id,
            'product_id' => $product->id,
            'quantity'   => $quantity,
            'price'      => $product->price,
            'sub_total'  => $subtotal
        ]);

        $total += $subtotal;
    }
    
    // 3- تحديث إجمالي الطلب
    $order->update(['total_price' => $total]);

    return redirect('/orders')->with('success', 'Order Created Successfully!');
}

   


//     public function updateOrder(Request $request)
// {
    
//     $request->validate([
//         'table_id'   => 'nullable|integer|exists:tables,id',
//         'phone'      => 'nullable|string|max:20',
//         'address'    => 'nullable|string|max:255',
//         'order_type' => 'required|in:dine_in,takeaway,delivery',
//         'items'      => 'required|array',
//         'items.*.product_id' => 'required|exists:products,id',
//         'items.*.quantity'   => 'required|integer|min:1',
//     ]);

    
//     $order = Order::findOrFail($request->id);

    
//     $order->table_id   = $request->table_id ?? 0;
//     $order->phone      = $request->phone;
//     $order->address    = $request->address;
//     $order->order_type = $request->order_type;
//     $order->save();

//     // 4) DELETE OLD ORDER ITEMS
//     OrderItem::where('order_id', $order->id)->delete();

//     // 5) ADD NEW ITEMS
//     $total = 0;

//     foreach ($request->items as $item) {

//         $product = Product::find($item['product_id']);

//         $orderItem = new OrderItem();
//         $orderItem->order_id  = $order->id;
//         $orderItem->product_id = $product->id;
//         $orderItem->price      = $product->price;
//         $orderItem->quantity   = $item['quantity'];
//         $orderItem->sup_total  = $product->price * $item['quantity'];
//         $orderItem->save();

//         $total += $orderItem->sup_total;
//     }

//     // 6) UPDATE ORDER TOTAL
//     $order->total_price = $total;
//     $order->save();

//     // 7) UPDATE INVOICE
//     $invoice = Invoice::where('order_id', $order->id)->first();

//     $invoice->table_number = $order->table_id;
//     $invoice->total_price  = $order->total_price;
//     $invoice->tax          = $request->tax ?? $invoice->tax;
//     $invoice->discount     = $request->discount ?? $invoice->discount;

//     $invoice->final_price  = $order->total_price + $invoice->tax - $invoice->discount;

//     $invoice->save();

//     return redirect()->back()->with('success', 'Order updated successfully');
// }

}
