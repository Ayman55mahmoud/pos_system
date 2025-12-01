<?php

namespace App\Http\Controllers;
use App\Models\order;
use App\Models\Table;
use App\Models\Invoice;
use App\Models\order_itme;
use App\Models\user;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashpoardController extends Controller
{ public function index()
    {
        $countoforder = order::whereDate('created_at', today())->count();

        $salescount = order::whereDate('created_at', today())->sum('total_price');

        $activeOrders = order::where('status', '=', 'Approved')->count();

        $busyTables = table::where('status', 'busy')->count();

        $freeTables = table::where('status', 'free')->count();

        $topProductsToday = order_itme::select('product_id', DB::raw('SUM(quantity) as total'))
            ->whereDate('created_at', today())
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('product')
            ->get();
            $labels = $topProductsToday->pluck('product.name'); // أسماء المنتجات
            $values = $topProductsToday->pluck('total');       // عدد المبيعات

        $orderTypesCount = order::select('order_type', DB::raw('COUNT(*) as total'))
            ->whereDate('created_at', today())
            ->groupBy('order_type')
            ->get();

        $todayCustomersCount = order::whereDate('created_at', today())
            ->distinct('phone')
            ->count('phone');

        $todayTax = invoice::whereDate('created_at', today())->sum('tax');

        $todayDiscount = invoice::whereDate('created_at', today())->sum('discount');
        $employees = User::whereIn('role', ['admin', 'cashair', 'waiter'])->count();


        return view('dashboard', compact(
            'countoforder',
            'salescount',
            'activeOrders',
            'busyTables',
            'freeTables',
            'topProductsToday',
            'orderTypesCount',
            'todayCustomersCount',
            'todayTax',
            'todayDiscount',
            'employees',
            'labels',
            'values'
        ));
    }

     public function topProductsToday(){
        $topProductsToday = order_itme::select('product_id', DB::raw('SUM(quantity) as total'))
            ->whereDate('created_at', today())
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->take(5)
            ->with('product')
            ->get();
            $labels = $topProductsToday->pluck('product.name'); // أسماء المنتجات
            $values = $topProductsToday->pluck('total');       // عدد المبيعات
            return view('pages.charts.chartjs',compact('labels','values'));
     }

    


    //     public function todayOrdersCount()
    // {
    //      $countoforder=order::whereDate('created_at', today())->count();
    //      return view('dashboard',compact('countoforder'));
    // }

    // public function todaySales()
    // {
    //     $salescount= order::whereDate('created_at', today())->sum('total_price');
    //     return view('dashboard',compact('salescount'));
    // }

    // public function activeOrders()
    // {
    //     return order::where('status', '!=', 'completed')->count();
    // }

    // public function busyTables()
    // {
    //     return table::where('status', 'busy')->count();
    // }

    // public function freeTables()
    // {
    //     return table::where('status', 'free')->count();
    // }

    // public function topProductsToday()
    // {
    //     return order_item::select('product_id', DB::raw('SUM(quantity) as total'))
    //             ->whereDate('created_at', today())
    //             ->groupBy('product_id')
    //             ->orderByDesc('total')
    //             ->take(5)
    //             ->with('product')
    //             ->get();
    // }

    // public function orderTypesCount()
    // {
    //     return order::select('order_type', DB::raw('COUNT(*) as total'))
    //             ->whereDate('created_at', today())
    //             ->groupBy('order_type')
    //             ->get();
    // }

    // public function todayCustomersCount()
    // {
    //     return order::whereDate('created_at', today())
    //                 ->distinct('phone')
    //                 ->count('phone');
    // }

    // public function todayTax()
    // {
    //     return invoice::whereDate('created_at', today())->sum('tax');
    // }

    // public function todayDiscount()
    // {
    //     return invoice::whereDate('created_at', today())->sum('discount');
    // }

}
