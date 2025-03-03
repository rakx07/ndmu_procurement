<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OfficeSeeder extends Seeder
{
    public function run()
    {
        DB::table('offices')->insert([
            ['name' => 'ICT Department', 'description' => 'Handles all IT-related tasks.'],
            ['name' => 'Business Office', 'description' => 'Manages financial transactions.Handles purchase and procurement.'],
            ['name' => 'HR Department', 'description' => 'Manages human resources and hiring.'],
            ['name' => 'Administration', 'description' => 'Oversees school-wide operations.'],
            ['name' => 'CAS Office', 'description' => 'College of Arts and Sciences'],
            ['name' => 'CEAC Office', 'description' => 'College of Engineering ,Architecture and Computing'],
            ['name' => 'CED Office', 'description' => 'College of Education'],
            ['name' => 'CBA Office', 'description' => 'College of Business Administration'],
            ['name' => 'PPO Office', 'description' => 'Physical Plant Office'],
            ['name' => 'CCC Office', 'description' => 'Champagnat Community COllege'],
            ['name' => 'QAPS', 'description' => 'Quality Assurance Office'],
        ]);
    }
}
