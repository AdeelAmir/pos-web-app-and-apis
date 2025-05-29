<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_expenditure_details', function (Blueprint $table) {
            $table->id();
            $table->integer('office_expenditure_id');
            $table->integer('expenditure_id');
            $table->double('amount');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_expenditure_details');
    }
};
