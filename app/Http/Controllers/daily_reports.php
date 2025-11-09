<?php

namespace App\Http\Controllers;

use App\Models\daily_report;

class daily_reports extends Controller
{
    public function index()
    {
        // نجيب كل التقارير الشهرية من الجدول
        $reports = daily_report::orderBy('report_date', 'desc')->get();

        
        return view('reports.monthly', compact('reports'));
    }
    public function lastmonth()
    {
        // نجيب  تقاريراخر شهر من الجدول
       $report = MonthlyReport::latest('report_date')->first();

        return view('reports.monthly', compact('reports'));
    }
}
