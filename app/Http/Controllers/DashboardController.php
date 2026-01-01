<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {

        $current_user = $request->user();
        $role = $current_user->employee->role;
        $isTrainee = $role === "trainee";

        // Projects
        $query = Project::with(['assignees.user', 'reviewers.user']);

        if ($isTrainee) {
            $query->whereHas('assignees', fn($q) =>
                $q->where('employees.id', $current_user->employee->id)
            );
        } else {
            if ($role === 'trainer') {
                $query->whereHas('reviewers', fn($q) =>
                    $q->where('employees.id', $current_user->employee->id)
                );
            }
        }

        $project_stats['Total'] = $query->count();
        $project_stats['Overdue'] = $query->clone()
            ->whereNull('completion_date')
            ->where('deadline', '<', now()->toDateString())
            ->count();
        $project_stats['Completed Late'] = $query->clone()
            ->whereNotNull('completion_date')
            ->whereColumn('completion_date', '>', 'deadline')
            ->count();
        $project_stats['Completed'] = $query->clone()
            ->whereNotNull('completion_date')
            ->whereColumn('completion_date', '<=', 'deadline')
            ->count();
        $project_stats['In Progress'] = $query->clone()
            ->whereNull('completion_date')
            ->where('deadline', '>=', now()->toDateString())
            ->count();

        // Get your limited projects
        $projects = $query->clone()
            ->whereNull('completion_date')
            ->orderBy('deadline', 'asc')
            ->limit(3)
            ->get();
        

        // Attendance
        if ($isTrainee) {
            $clock_in_late = [];
            $clock_in_missing = [];

            $today_record = AttendanceRecord::where('employee_id', $current_user->employee->id)
                                            ->today()
                                            ->first();
        } else {
            $records = AttendanceRecord::today()->with('employee.user')->get();

            $trainees = Employee::where("role", "trainee")->with('user')->get();

            $clock_in_late = [];
            $clock_in_missing = [];

            foreach ($trainees as $trainee) {
                $record = $records->firstWhere('employee_id', $trainee->id);

                if ($record) {
                    $lateThreshold = Carbon::createFromTime(8, 30)->setDate(
                        Carbon::parse($record->date)->year, 
                        Carbon::parse($record->date)->month,
                        Carbon::parse($record->date)->day
                    );

                    if ($record->punch_in) {
                        if (Carbon::parse($record->punch_in)->gt($lateThreshold)) {
                            $clock_in_late[] = $record;
                        }
                    } else {
                        $clock_in_missing[] = $record;
                    }
                } else {
                    // No record means missing clock-in
                    $clock_in_missing[] = (object)[
                        'employee'  => $trainee             ,
                        'date'      => now()->toDateString(),
                        'punch_in'  => null                 ,
                        'punch_out' => null                 ,
                    ];
                }
            }

            $today_record = null;
        }

        // Leaves
        $leave_query = AttendanceRecord::where('on_leave', true)
                                        ->whereBetween('date', [
                                            Carbon::now()->startOfMonth(), 
                                            Carbon::now()->endOfMonth()
                                        ]);

        if ($isTrainee) {
            $leave_query->where('employee_id', $current_user->employee->id);
        } else {
            $leave_query->with('employee.user');
        }

        $leaves = $leave_query->limit(10)->get();

        return Inertia::render('Dashboard/Dashboard', [
            'projects'         => $projects        ,
            'clock_in_late'    => $clock_in_late   ,
            'clock_in_missing' => $clock_in_missing,
            'isTrainee'        => $isTrainee       ,
            'today_record'     => $today_record    ,
            'project_stats'    => $project_stats   ,
            'leaves'           => $leaves          ,
        ]);
    }
}
