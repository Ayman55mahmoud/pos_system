<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();

            $table->date('report_date')->unique();

            // المبيعات
            $table->integer('total_orders')->default(0);
            $table->decimal('total_sales', 10, 2)->default(0);
            $table->decimal('total_cash', 10, 2)->default(0);
            $table->decimal('total_card', 10, 2)->default(0);

            // الأرباح 
            $table->decimal('total_revenue', 10, 2)->default(0);
            $table->decimal('total_refunds', 10, 2)->default(0); // المرتجعات لو موجودة

            // المجموع النهائي بعد الخصم من المرتجعات
            $table->decimal('net_income', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
