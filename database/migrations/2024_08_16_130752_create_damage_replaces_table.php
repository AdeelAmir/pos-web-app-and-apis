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
        Schema::create('damage_replaces', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id');
            $table->integer('city_id');
            $table->date('date');
            $table->double('grand_total');
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
        Schema::dropIfExists('damage_replaces');
    }
};
