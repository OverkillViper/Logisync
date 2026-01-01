<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\User;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $filePath = database_path('seeders/data/projects.csv');

        if (!file_exists($filePath)) {
            $this->command->error("CSV file not found at: $filePath");
            return;
        }

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // skip header

        while (($data = fgetcsv($file)) !== false) {
            [
                $assignees,         // index 1
                $taskName,          // index 2
                $startDate,         // index 3
                $deadline,          // index 4
                $completionDate,    // index 5
                $status,            // index 0
                $comments,          // index 6
                $reviewers,         // index 7
            ] = $data;

            // Create project
            $project = Project::create([
                'name' => $taskName,
                'start_date' => Carbon::parse($startDate)->format('Y-m-d'),
                'deadline' => Carbon::parse($deadline)->format('Y-m-d'),
                'completion_date' => Carbon::parse($completionDate)->format('Y-m-d'),
                'created_by' => 1,
            ]);

            // Attach assignees
            $assigneeNames = array_filter(array_map('trim', explode(',', $assignees)));
            foreach ($assigneeNames as $name) {
                $user = User::where('name', $name)->first();
                if ($user && $user->employee) {
                    $employee = $user->employee;
                    $project->assignees()->syncWithoutDetaching([$employee->id]);
                } else {
                    $this->command->warn("    Employee not found for user: $name");
                }
            }

            // Attach reviewers
            $reviewerNames = array_filter(array_map('trim', explode(',', $reviewers)));
            $reviewerUserId = null;

            foreach ($reviewerNames as $name) {
                $user = User::where('name', $name)->first();
                if ($user && $user->employee) {
                    $employee = $user->employee;
                    $reviewerUserId = $user->id;
                    $project->reviewers()->syncWithoutDetaching([$employee->id]);
                } else {
                    $this->command->warn("    Employee not found for user: $name");
                }
            }

            // Create comment if available
            if (!empty($comments) && $reviewerUserId) {
                ProjectComment::create([
                    'project_id' => $project->id,
                    'user_id'    => $reviewerUserId,
                    'comment'    => $comments,
                ]);
            }
        }

        fclose($file);

        $this->command->info('Projects seeded successfully!');
    }
}
