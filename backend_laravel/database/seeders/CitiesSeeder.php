<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ['name' => 'دمشق'],
            ['name' => 'حلب'],
            ['name' => 'حمص'],
            ['name' => 'حماه'],
            ['name' => 'اللاذقية'],
            ['name' => 'طرطوس'],
            ['name' => 'إدلب'],
            ['name' => 'الرقة'],
            ['name' => 'دير الزور'],
            ['name' => 'الحسكة'],
            ['name' => 'درعا'],
            ['name' => 'السويداء'],
            ['name' => 'ريف دمشق'],
            ['name' => 'القنيطرة'],
            ['name' => 'القامشلي'],
            ['name' => 'منبج'],
            ['name' => 'الباب'],
            ['name' => 'الدرباسية'],
            ['name' => 'جبل الزاوية'],
        ];

        DB::table('cities')->insert($cities);
    }
    
}
