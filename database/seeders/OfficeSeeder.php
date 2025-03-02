<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    public function run()
    {
        DB::table('offices')->insert([
            ['name' => 'IT Department', 'description' => 'Handles all IT-related tasks.'],
            ['name' => 'Accounting Office', 'description' => 'Manages financial transactions.'],
            ['name' => 'Procurement Office', 'description' => 'Handles purchase and procurement.'],
            ['name' => 'HR Department', 'description' => 'Manages human resources and hiring.'],
            ['name' => 'Administration', 'description' => 'Oversees school-wide operations.'],
        ]);
    }
}
