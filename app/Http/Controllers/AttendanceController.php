<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\AttendanceRecord;
use Inertia\Inertia;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AttendanceController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $month = $request->month ?? now()->month;
        $year  = $request->year ?? now()->year;

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth   = Carbon::create($year, $month, 1)->endOfMonth();

        $employee = $request->user()->employee;
        $isTrainee = $employee->role === "trainee";

        // For trainees
        $today_record = $isTrainee  ? AttendanceRecord::where('employee_id', $employee->id)
                                            ->today()
                                            ->first()
                                        : null;

        // For admins/trainers viewing trainees
        if (!$isTrainee && $request->trainee_id) {
            $selectedTrainee = Employee::where("role", "trainee")->with('user')->find($request->trainee_id);

            if ($selectedTrainee) {
                $employee = $selectedTrainee;
            }
        }

        $trainees = Employee::where("role", "trainee")->with('user')->get();

        // Get attendance records for the selected month
        $records = AttendanceRecord::where('employee_id', $employee->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with('employee')
            ->orderBy('date', 'asc')
            ->get()
            ->keyBy('date');
        
        $days = [];
        $today = now()->toDateString();

        if ($records) {
            for ($date = $startOfMonth->copy(); $date->lte($endOfMonth); $date->addDay()) {
                $dateString = $date->toDateString() . " 00:00:00";

                $record = $records->get($dateString);

                $days[] = [
                    'id'         => $record->id ?? null,
                    'date'       => $dateString,
                    'punch_in'   => $record->punch_in ?? null,
                    'punch_out'  => $record->punch_out ?? null,
                    'is_future'  => $date->toDateString() > $today,
                    'has_record' => $record !== null,
                    'on_leave'   => $record->on_leave ?? false,
                    'holiday'    => $record->holiday ?? false,
                ];
            }
        }
        
        if (!$isTrainee) {
            if (!$request->trainee_id || $records->count() == 0) {
                $days = [];
            }
        }

        $stats = null;

        if (count($days)) {
            $lateThreshold = Carbon::createFromTime(8, 30);

            $stats = [
                'late' => 0,
                'missing_clock_in' => 0,
            ];

            foreach ($days as $day) {
                // Skip future dates
                if ($day['is_future']) {
                    continue;
                }

                // Skip weekends
                if (Carbon::parse($day['date'])->isWeekend()) {
                    continue;
                }

                // Skip leave days
                if ($day['on_leave']) {
                    continue;
                }

                // Clock-in logic
                if ($day['punch_in']) {
                    $lateThreshold->setDate(
                        Carbon::parse($day['date'])->year, 
                        Carbon::parse($day['date'])->month,
                         Carbon::parse($day['date'])->day
                    );
                    if (Carbon::parse($day['punch_in'])->gt($lateThreshold)) {
                        $stats['late']++;
                    }
                } else {
                    $stats['missing_clock_in']++;
                }
            }
        }

        return Inertia::render('Attendance/Index', [
            'today_record'       => $today_record,
            'attendance_stats'   => $stats,
            'attendance_records' => $days,
            'selectedMonth'      => $month,
            'selectedYear'       => $year,
            'selectedEmployee'   => $employee,
            'trainees'           => $trainees,
            'isTrainee'          => $isTrainee,
        ]);
    }

    public function punch(Request $request)
    {
        $employee_id = $request->user()->employee->id;
        $today = now();

        $attendance = AttendanceRecord::where('employee_id', $employee_id)
                            ->whereDate('date', $today)
                            ->first();

        if (!$attendance) {
            $attendance = AttendanceRecord::create([
                'employee_id'   => $employee_id,
                'date'          => now(),
                'punch_in'      => now(),
                'punch_out'     => null
            ]);

            return redirect()->back()->with('success', 'Successfully punched in.');
        } elseif (!$attendance->punch_in) {
            $attendance->update([
                'punch_in' => now()
            ]);
            
            return redirect()->back()->with('success', 'Successfully punched in.');
        } else {
            $attendance->update([
                'punch_out' => now()
            ]);

            return redirect()->back()->with('success', 'Successfully punched out.');
        }   
    }

    public function markOnLeave(Request $request)
    {
        if (!$request->employee_id) {
            return redirect()->back()->with('error', 'Employee ID is required.');
        } else {
            if (!$request->record['id']) {
                AttendanceRecord::create([
                    'employee_id' => $request->employee_id,
                    'date'        => $request->record['date'],
                    'punch_in'    => null,
                    'punch_out'   => null,
                    'holiday'     => false,
                    'on_leave'    => true
                ]);
            } else {
                $record = AttendanceRecord::find($request->record['id']);
                $record->update([
                    'on_leave' => true
                ]);
            }
        }

        return redirect()->back()->with('success', 'Marked as on leave.');
    }

    public function clearOnLeave(Request $request)
    {
        $record = AttendanceRecord::find($request->record['id']);
        $record->update([
            'on_leave' => false
        ]);

        return redirect()->back()->with('success', 'Cleared on leave.');
    }

    public function markOnHoliday(Request $request)
    {
        if (!$request->employee_id) {
            return redirect()->back()->with('error', 'Employee ID is required.');
        } else {
            if (!$request->record['id']) {
                AttendanceRecord::create([
                    'employee_id' => $request->employee_id,
                    'date'        => $request->record['date'],
                    'punch_in'    => null,
                    'punch_out'   => null,
                    'on_leave'    => false,
                    'holiday'     => true
                ]);
            } else {
                $record = AttendanceRecord::find($request->record['id']);
                $record->update([
                    'holiday' => true
                ]);
            }
        }

        return redirect()->back()->with('success', 'Marked as on holiday.');
    }

    public function clearOnHoliday(Request $request)
    {
        $record = AttendanceRecord::find($request->record['id']);
        $record->update([
            'holiday' => false
        ]);

        return redirect()->back()->with('success', 'Cleared on holiday.');
    }

    public function todaysAttendance(Request $request)
    {
        $this->authorize('view', AttendanceRecord::class);

        $records = AttendanceRecord::today()->with('employee.user')->get();
        $trainees = Employee::where("role", "trainee")->with('user')->get();

        $clock_in_time = [];
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
                    } else {
                        $clock_in_time[] = $record;
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

        return Inertia::render('Attendance/TodaysAttendance', [
            'attendance_record' => $records          ,
            'clock_in_time'     => $clock_in_time    ,
            'clock_in_late'     => $clock_in_late    ,
            'clock_in_missing'  => $clock_in_missing ,   
        ]);
    }
}
