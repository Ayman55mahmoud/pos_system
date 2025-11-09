<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeneratemonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generatemonthly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
{
    $startOfMonth = Carbon::now()->startOfMonth();
    $endOfMonth = Carbon::now()->endOfMonth();
    $monthName = Carbon::now()->format('F Y'); // مثال: "November 2025"

    // احصائيات الشهر الحالي
    $totalOrders = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
    $totalSales = Invoice::whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('final_price');
    $totalCash = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->where('pay_method', 'cash')
        ->sum('amount');
    $totalCard = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->where('pay_method', 'card')
        ->sum('amount');
    $totalRefunds = Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
        ->where('status', 'failed')
        ->sum('amount');

    $netIncome = $totalSales - $totalRefunds;

    // إنشاء أو تحديث تقرير الشهر
    DailyReport::updateOrCreate(
        ['report_date' => $startOfMonth], // أو اعمل حقل report_month لو حابب
        [
            'total_orders' => $totalOrders,
            'total_sales' => $totalSales,
            'total_cash' => $totalCash,
            'total_card' => $totalCard,
            'total_revenue' => $totalSales,
            'total_refunds' => $totalRefunds,
            'net_income' => $netIncome,
        ]
    );

    $this->info(" Monthly report for {$monthName} generated successfully!");
}


}
