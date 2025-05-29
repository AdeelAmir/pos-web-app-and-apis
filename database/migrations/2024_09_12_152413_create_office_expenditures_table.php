<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_expenditures', function (Blueprint $table) {
            $table->id();
            $table->integer('seller_id')->nullable();
            $table->date('expenditure_date');
            $table->string('type')->comment('Office, Seller');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_expenditures');
    }
};
