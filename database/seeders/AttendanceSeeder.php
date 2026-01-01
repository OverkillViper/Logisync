<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee_in_out_col = [
            // Name                     , IN COL, OUT COL, BATCH          , CSV FILE PATH
            ['Razi Mudassir'            , 1     , 2      , 'December 2024', database_path('seeders/data/december_2024_attendance.csv')],
            ['Md. Niaz Mahmud Sanid'    , 3     , 4      , 'December 2024', database_path('seeders/data/december_2024_attendance.csv')],
            ['Md. Mushfiqur Rahman'     , 5     , 6      , 'December 2024', database_path('seeders/data/december_2024_attendance.csv')],
            ['Shakil Ahmed Shawon'      , 9     , 10     , 'December 2024', database_path('seeders/data/december_2024_attendance.csv')],
            ['Sattik Debnath'           , 7     , 8      , 'December 2024', database_path('seeders/data/december_2024_attendance.csv')],
            ['Md. Idrak Efaz'           , 2     , 3      , 'March 2025'   , database_path('seeders/data/march_2025_attendance.csv'   )],
            ['Md. Misbahul Hasan Jinan' , 4     , 5      , 'March 2025'   , database_path('seeders/data/march_2025_attendance.csv'   )],
            ['Shakib Hasan'             , 6     , 7      , 'March 2025'   , database_path('seeders/data/march_2025_attendance.csv'   )],
            ['Nafiz Bin Hasanat'        , 2     , 3      , 'May 2025'     , database_path('seeders/data/may_2025_attendance.csv'     )],
            ['Rakibul Hasan'            , 4     , 5      , 'May 2025'     , database_path('seeders/data/may_2025_attendance.csv'     )],
            ['Osama Abrar'              , 6     , 7      , 'August 2025'  , database_path('seeders/data/august_2025_attendance.csv'  )],
            ['Chowdhury Raiyath'        , 2     , 3      , 'August 2025'  , database_path('seeders/data/august_2025_attendance.csv'  )],
            ['Fatema Tuz Zohora Soma'   , 4     , 5      , 'August 2025'  , database_path('seeders/data/august_2025_attendance.csv'  )],
        ];

        foreach ($employee_in_out_col as $employee) {
            if (!file_exists($employee[4])) {
                $this->command->error("CSV file not found at: $employee[4]");
                return;
            }

            $file = fopen($employee[4], 'r');
            $header = fgetcsv($file); // skip header

            while (($data = fgetcsv($file)) !== false) {
                $date = $data[0];
                $cleanDate = date('Y-m-d', strtotime($date));

                $employee_id = User::where('name', $employee[0])->first()->id;
                $punch_in  = !empty($data[$employee[1]])
                    ? date('Y-m-d H:i:s', strtotime("$cleanDate " . $data[$employee[1]]))
                    : null;

                $punch_out = !empty($data[$employee[2]])
                    ? date('Y-m-d H:i:s', strtotime("$cleanDate " . $data[$employee[2]]))
                    : null;

                AttendanceRecord::create([
                    'employee_id'   => $employee_id,
                    'date'          => $date,
                    'punch_in'      => $punch_in,
                    'punch_out'     => $punch_out
                ]);
            }
            fclose($file);
            $this->command->info("Attendance for {$employee[0]} seeded successfully!");
        }

    }
}
