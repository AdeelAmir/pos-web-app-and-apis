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
        Schema::create('damage_replace_items', function (Blueprint $table) {
            $table->id();
            $table->integer('damage_id');
            $table->integer('product_id');
            $table->double('retail_price');
            $table->integer('quantity');
            $table->double('sub_total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('damage_replace_items');
    }
};
