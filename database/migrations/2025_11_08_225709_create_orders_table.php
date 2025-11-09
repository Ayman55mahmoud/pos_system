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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->string('order_number')->unique();
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->enum('order_type', ['dine_in', 'delivery', 'takeaway']);
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('table_id')->references('id')->on('tables')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
