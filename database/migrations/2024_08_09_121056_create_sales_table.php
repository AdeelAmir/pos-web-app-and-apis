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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->integer('city_id');
            $table->integer('selling_city_id')->nullable();
            $table->date('date');
            $table->boolean('bonus');
            $table->double('grand_total');
            $table->string('payment_type')->comment('retail_price, wholesale_price, extra_price');
            $table->string('office_payment_type')->nullable()->comment('Cash, Credit');
            $table->string('type')->default('Normal')->comment('Normal, Office');
            $table->boolean('loan')->default(0);
            $table->string('status', 25)->default('Pending')->comment('Pending, Completed');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
