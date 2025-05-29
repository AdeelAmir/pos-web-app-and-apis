<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->integer('shop_id');
            $table->date('date');
            $table->double('grand_total');
            $table->string('price_type')->comment('retail_price, wholesale_price, extra_price	');
            $table->string('orignal_payment_type')->comment('retail_price, wholesale_price, extra_price');
            $table->string('payment_type')->comment('Cash, Credit');
            $table->string('sale_type')->comment('Stock, Bonus');
            $table->integer('loan')->default('0')->comment('0 = no, 1 = yes');
            $table->string('status')->default('Pending')->comment('Pending, Completed');
            $table->timestamps();
            $table->softDeletes();
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
