<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PackageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('packages')->insert([
            'name' => 'Basic',
            'description' => 'Basic packages',
            'price' => 100,
            'type' => '1 Month',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('packages')->insert([
            'name' => 'Standard',
            'description' => 'Standard packages',
            'price' => 200,
            'type' => '6 Months',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('packages')->insert([
            'name' => 'Premium',
            'description' => 'Premium packages',
            'price' => 300,
            'type' => '1 Year',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
