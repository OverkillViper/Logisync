<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $employees = [
            [ 'name' => 'LogiSync Admin'           , 'role' => 'admin'   , 'employee_id' => '000000', 'email' => 'admin@logisync.com'                   , 'designation' => 'Admin'                     , 'joining_date' => '01-Jan-2017', 'contact_number' => '+88 00000000000' , 'emergency_contact_number' => '+88 00000000000', 'batch' => 'May 2017'      , 'emergency_ralation' => 'Self'            ],
            [ 'name' => 'Jahirul Aziz'             , 'role' => 'trainer' , 'employee_id' => '170501', 'email' => 'jahirul@ulkasemi.com'                 , 'designation' => 'Senior Technical Manager'  , 'joining_date' => '01-May-2017', 'contact_number' => '+88 01674901649' , 'emergency_contact_number' => '+88 01673620240', 'batch' => 'May 2017'      , 'emergency_ralation' => 'Spouse'          ],
            [ 'name' => 'Khan MD Tahmid'           , 'role' => 'trainer' , 'employee_id' => '190703', 'email' => 'tahmid@ulkasemi.com'                  , 'designation' => 'Member of Technical Staff' , 'joining_date' => '01-Jul-2019', 'contact_number' => '+88 01960685384' , 'emergency_contact_number' => '+88 01912229759', 'batch' => 'July 2019'     , 'emergency_ralation' => 'Brother'         ],
            [ 'name' => 'Muh Emrul Kayes'          , 'role' => 'trainer' , 'employee_id' => '190704', 'email' => 'kayes@ulkasemi.com'                   , 'designation' => 'Member of Technical Staff' , 'joining_date' => '01-Jul-2019', 'contact_number' => '+88 01675553287' , 'emergency_contact_number' => '+88 01819741147', 'batch' => 'July 2019'     , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'A. K. M. Ferdous'         , 'role' => 'trainer' , 'employee_id' => '211103', 'email' => 'a.k.m.ferdous@ulkasemi.com'           , 'designation' => 'Senior Engineer'           , 'joining_date' => '01-Nov-2021', 'contact_number' => '+88 01551030629' , 'emergency_contact_number' => '+88 01551030627', 'batch' => 'November 2021' , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Md. Mahadi Hasan Bhuiyan' , 'role' => 'trainer' , 'employee_id' => '211102', 'email' => 'md.mahadi.hasan.bhuiyan@ulkasemi.com' , 'designation' => 'Senior Engineer'           , 'joining_date' => '01-Nov-2021', 'contact_number' => '+88 01610147010' , 'emergency_contact_number' => '+88 01795350998', 'batch' => 'November 2021' , 'emergency_ralation' => 'Sister'          ],
            [ 'name' => 'Md. Asif Imran Khan'      , 'role' => 'trainer' , 'employee_id' => '220205', 'email' => 'md.asif.imran.khan@ulkasemi.com'      , 'designation' => 'Senior Engineer'           , 'joining_date' => '15-Feb-2022', 'contact_number' => '+88 01679409094' , 'emergency_contact_number' => '+88 01715350614', 'batch' => 'February 2022' , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Jabid Asraf'              , 'role' => 'trainer' , 'employee_id' => '220207', 'email' => 'jabid.asraf@ulkasemi.com'             , 'designation' => 'Senior Engineer'           , 'joining_date' => '15-Feb-2022', 'contact_number' => '+88 01632378895' , 'emergency_contact_number' => '+88 01712054708', 'batch' => 'February 2022' , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'A. B. M. Safiulla'        , 'role' => 'trainer' , 'employee_id' => '221105', 'email' => 'a.b.m.safiulla@ulkasemi.com'          , 'designation' => 'Engineer'                  , 'joining_date' => '16-Nov-2022', 'contact_number' => '+88 01757149992' , 'emergency_contact_number' => '+88 01730470937', 'batch' => 'November 2022' , 'emergency_ralation' => 'Spouse'          ],
            [ 'name' => 'Shadman Hasan Khan'       , 'role' => 'trainer' , 'employee_id' => '230701', 'email' => 'shadman.hasan.khan@ulkasemi.com'      , 'designation' => 'Engineer'                  , 'joining_date' => '03-Jul-2023', 'contact_number' => '+88 01558002106' , 'emergency_contact_number' => '+88 01681389645', 'batch' => 'July 2023'     , 'emergency_ralation' => 'Sister'          ],
            [ 'name' => 'Mariam Hossain Nijhum'    , 'role' => 'trainee' , 'employee_id' => '240802', 'email' => 'mariom.hossain.nijhum@ulkasemi.com'   , 'designation' => 'Assistant Engineer'        , 'joining_date' => '19-Aug-2024', 'contact_number' => '+88 01627576310' , 'emergency_contact_number' => '+88 01816151926', 'batch' => 'August 2024'   , 'emergency_ralation' => 'Mother'          ],
            [ 'name' => 'Aalolika Roy Chowdhury'   , 'role' => 'trainee' , 'employee_id' => '240803', 'email' => 'aalolika.roy.chowdhury@ulkasemi.com'  , 'designation' => 'Assistant Engineer'        , 'joining_date' => '19-Aug-2024', 'contact_number' => '+88 01970335722' , 'emergency_contact_number' => '+88 01712226985', 'batch' => 'August 2024'   , 'emergency_ralation' => 'Mother'          ],
            [ 'name' => 'Razi Mudassir'            , 'role' => 'trainee' , 'employee_id' => '241213', 'email' => 'razi.mudassir@ulkasemi.com'           , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-Dec-2024', 'contact_number' => '+88 01773427930' , 'emergency_contact_number' => '+88 01717678117', 'batch' => 'December 2024' , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Md. Niaz Mahmud Sanid'    , 'role' => 'trainee' , 'employee_id' => '241214', 'email' => 'md.niaz.mahmud@ulkasemi.com'          , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-Dec-2024', 'contact_number' => '+88 01770195952' , 'emergency_contact_number' => '+88 01920608129', 'batch' => 'December 2024' , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Md. Mushfiqur Rahman'     , 'role' => 'trainee' , 'employee_id' => '241215', 'email' => 'md.mushfiqur@ulkasemi.com'            , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-Dec-2024', 'contact_number' => '+88 01311456006' , 'emergency_contact_number' => '+88 01730915396', 'batch' => 'December 2024' , 'emergency_ralation' => 'Spouse'          ],
            [ 'name' => 'Shakil Ahmed Shawon'      , 'role' => 'trainee' , 'employee_id' => '241216', 'email' => 'shakil.ahmed.shawon@ulkasemi.com'     , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-Dec-2024', 'contact_number' => '+88 01768012987' , 'emergency_contact_number' => '+88 01718195507', 'batch' => 'December 2024' , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Sattik Debnath'           , 'role' => 'trainee' , 'employee_id' => '241217', 'email' => 'sattik.debnath@ulkasemi.com'          , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-Dec-2024', 'contact_number' => '+88 01521507189' , 'emergency_contact_number' => '+88 01767334444', 'batch' => 'December 2024' , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Md. Idrak Efaz'           , 'role' => 'trainee' , 'employee_id' => '250302', 'email' => 'idrak.efaz@ulkasemi.com'              , 'designation' => 'Assistant Engineer'        , 'joining_date' => '10-Mar-2025', 'contact_number' => '+88 0186069529'  , 'emergency_contact_number' => '+88 01915568470', 'batch' => 'March 2025'    , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Md. Misbahul Hasan Jinan' , 'role' => 'trainee' , 'employee_id' => '250303', 'email' => 'misbahul.hasan.jinan@ulkasemi.com'    , 'designation' => 'Assistant Engineer'        , 'joining_date' => '10-Mar-2025', 'contact_number' => '+88 01521565587' , 'emergency_contact_number' => '+88 01712513084', 'batch' => 'March 2025'    , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Shakib Hasan'             , 'role' => 'trainee' , 'employee_id' => '250305', 'email' => 'shakib.hasan@ulkasemi.com'            , 'designation' => 'Assistant Engineer'        , 'joining_date' => '10-Mar-2025', 'contact_number' => '+88 01624706203' , 'emergency_contact_number' => '+88 01718588198', 'batch' => 'March 2025'    , 'emergency_ralation' => 'Brother(Cousin)' ],
            [ 'name' => 'Nafiz Bin Hasanat'        , 'role' => 'trainee' , 'employee_id' => '250536', 'email' => 'nafiz.bin.hasanat@ulkasemi.com'       , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-May-2025', 'contact_number' => '+88 01829447981' , 'emergency_contact_number' => '+88 01892433592', 'batch' => 'May 2025'      , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Rakibul Hasan'            , 'role' => 'trainee' , 'employee_id' => '250538', 'email' => 'rakibul.hasan@ulkasemi.com'           , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-May-2025', 'contact_number' => '+88 01338768845' , 'emergency_contact_number' => '+88 01730335250', 'batch' => 'May 2025'      , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Osama Abrar'              , 'role' => 'trainee' , 'employee_id' => '250523', 'email' => 'osama.abrar@ulkasemi.com'             , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-May-2025', 'contact_number' => '+88 01841482313' , 'emergency_contact_number' => '+88 01718795683', 'batch' => 'August 2025'   , 'emergency_ralation' => 'Father'          ],
            [ 'name' => 'Chowdhury Raiyath'        , 'role' => 'trainee' , 'employee_id' => '250527', 'email' => 'chowdhuri.raiyath@ulkasemi.com'       , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-May-2025', 'contact_number' => '+88 01521521726' , 'emergency_contact_number' => '+88 01682414139', 'batch' => 'August 2025'   , 'emergency_ralation' => 'Brother'         ],
            [ 'name' => 'Fatema Tuz Zohora Soma'   , 'role' => 'trainee' , 'employee_id' => '250529', 'email' => 'fatema.tuz.zohora.soma@ulkasemi.com'  , 'designation' => 'Assistant Engineer'        , 'joining_date' => '05-May-2025', 'contact_number' => '+88 01841856513' , 'emergency_contact_number' => '+88 01775499140', 'batch' => 'August 2025'   , 'emergency_ralation' => 'Mother'          ],
        ];

        foreach ($employees as $data) {
            // Create the user
            $user = User::create([
                'name'     => $data['name'],
                'email'    => $data['email'],
                'password' => Hash::make('password'),
            ]);

            // Parse the date string into a Carbon instance
            $joinDate = Carbon::createFromFormat('j-M-Y', $data['joining_date']);

            // Create the related employee record
            $employee = $user->employee; // get the existing related employee
            $batch = Batch::where('name', $data['batch'])->first();

            $employee->update([
                'employee_id'                => $data['employee_id'],
                'profile_picture'            => null,
                'joining_date'               => $joinDate,
                'contact_number'             => $data['contact_number'],
                'emergency_contact_number'   => $data['emergency_contact_number'],
                'emergency_contact_relation' => $data['emergency_ralation'],
                'designation'                => $data['designation'],
                'role'                       => $data['role'],
                'batch_id'                   => $batch->id,
            ]);
        }

        $supervisorAssignments = [
            'Mariam Hossain Nijhum'    => 'Jabid Asraf',
            'Aalolika Roy Chowdhury'   => 'A. B. M. Safiulla',
            'Razi Mudassir'            => 'Md. Mahadi Hasan Bhuiyan',
            'Md. Niaz Mahmud Sanid'    => 'A. K. M. Ferdous',
            'Md. Mushfiqur Rahman'     => 'Md. Asif Imran Khan',
            'Shakil Ahmed Shawon'      => 'A. K. M. Ferdous',
            'Sattik Debnath'           => 'A. B. M. Safiulla',
            'Md. Idrak Efaz'           => 'Jabid Asraf',
            'Md. Misbahul Hasan Jinan' => 'Md. Mahadi Hasan Bhuiyan',
            'Shakib Hasan'             => 'Shadman Hasan Khan',
            'Rakibul Hasan'            => 'Md. Asif Imran Khan',
            'Nafiz Bin Hasanat'        => 'A. B. M. Safiulla',
        ];

        foreach ($supervisorAssignments as $employeeName => $supervisorName) {
            $employee = Employee::whereHas('user', function ($q) use ($employeeName) {
                $q->where('name', $employeeName);
            })->first();

            $supervisor = User::where('name', $supervisorName)->first();

            if ($employee && $supervisor) {
                $employee->supervisor_id = $supervisor->id;
                $employee->save();
            } else {
                if (!$employee) $this->command->warn("Employee not found: $employeeName");
                if (!$supervisor) $this->command->warn("Supervisor not found: $supervisorName");
            }
        }
    }
}