<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\BatchSeeder;
use Database\Seeders\EmployeeSeeder;
use Database\Seeders\ProjectSeeder;
use Database\Seeders\EvaluationCriteriaSeeder;
use Database\Seeders\AttendanceSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            BatchSeeder::class,
            EmployeeSeeder::class,
            ProjectSeeder::class,
            EvaluationCriteriaSeeder::class,
            AttendanceSeeder::class,
        ]);
    }
}
