<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Employee;
use App\Models\Evaluation;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        $roles = $request->input('roles', ['admin', 'trainer', 'trainee']); // Get selected roles (array)

        $employees = Employee::with('user')
            ->when(!empty($roles), function ($query) use ($roles) {
                $query->whereIn('role', $roles);
            })
            ->orderBy('name')
            ->get();

        return Inertia::render('Employee/Index', [
            'employees' => $employees,
            'filters' => [
                'roles' => $roles,
            ],
        ]);
    }

    /**
     * Show the employee details.
     */
    public function show(Request $request, Employee $employee)
    {
        // Eager load relationships
        $employee->load([
            'user',
            'supervisor.employee',
            'batch',
            'assignedProjects.assignees.user',
            'assignedProjects.reviewers.user',
            'reviewingProjects.assignees.user',
            'reviewingProjects.reviewers.user',
            'subordinates.user',
        ]);

        $current_employee = $request->user()->employee;

        // Load available trainers
        $trainers = $current_employee->role != 'trainee' ? Employee::with('user')
                            ->whereIn('role', ['trainer'])
                            ->orderBy('name')
                            ->get() : [];
    

        $trainerOptions = $current_employee->role != 'trainee' ? $trainers->map(function($trainer) {
            return [
                'label' => $trainer->user->name, // display name
                'value' => $trainer->user_id,     // value to bind
            ];
        }) : [];

        // Load available batches
        $batches = $current_employee->role != 'trainee' ? Batch::orderBy('id')->get() : [];

        $batchOptions = $current_employee->role != 'trainee' ? $batches->map(function($batch) {
            return [
                'label' => $batch->name,    // display name
                'value' => $batch->id,      // value to bind
            ];
        }) : [];

        // Load evaluations for the employee
        if (!$employee->batch) {
            $evaluations = collect(); // No evaluations if not assigned to any batch
        } else {
            $evaluations = Evaluation::where('batch_id', $employee->batch->id)
                ->with(['scores' => function ($q) use ($employee) {
                    $q->where('trainee_id', $employee->id)
                    ->with('criteria');
                }, 'remarks'])
                ->orderBy('start_date', 'asc')
                ->get();
        }
        // Load yearly attendance
        $year = now()->year;

        $records = AttendanceRecord::where('employee_id', $employee->id)
                                   ->whereYear('date', $year)
                                   ->where(function ($q) {
                                       $q->whereNotNull('punch_in')
                                       ->orWhereNotNull('punch_out');
                                   })
                                   ->get();

        $monthlyAverages = collect(range(1, 12))->map(function ($month) use ($records, $year) {
            $monthRecords = $records->filter(function ($r) use ($month, $year) {
                $date = Carbon::parse($r->date);

                return $date->month === $month
                    && !$date->isWeekend()
                    && !$r->on_leave
                    && $date->lte(now());
            });

            $avgIn = $this->averageTime($monthRecords, 'punch_in');
            $avgOut = $this->averageTime($monthRecords, 'punch_out');

            return [
                'month' => Carbon::create($year, $month, 1)->format('M'),
                'avg_punch_in' => $avgIn,
                'avg_punch_out' => $avgOut,
            ];
        });

        // Employee leave history
        $leaves = AttendanceRecord::where('employee_id', $employee->id)
                                  ->where('on_leave', true)
                                  ->orderBy('date', 'desc')
                                  ->get();

        return Inertia::render('Employee/EmployeeProfile', [
            'employee'          => $employee       ,
            'trainers'          => $trainerOptions ,
            'batches'           => $batchOptions   ,
            'evaluations'       => $evaluations    ,
            'monthlyAttendance' => $monthlyAverages,
            'leaveHistory'      => $leaves,
        ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name'                       => 'required|string|max:255',
            'employee_id'                => 'required|string|max:255',
            'designation'                => 'required|string|max:255',
            'emergency_contact_number'   => 'required|string|max:255',
            'emergency_contact_relation' => 'required|string|max:255',
            'joining_date'               => 'required|date|max:255'  ,
            'contact_number'             => 'required|string|max:255',
        ]);

        $employee->update($validated);
        $employee->user->update([
            'name' => $validated['name'],
        ]);

        return redirect()->back()
            ->with('success', 'Employee updated successfully.');
    }

    public function updateRole(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,trainer,trainee',
        ]);

        $employee->update([
            'role' => $validated['role'],
        ]);

        if ($validated['role'] != 'trainee') {
            $employee->supervisor_id = null;
            $employee->save();
        }

        notify(
            $employee->user,
            "Role Updated",
            "Your role has been updated to {$validated['role']}.",
            route('employees.show', $employee->id),
            'profile'
        );

        return redirect()->back()
            ->with('success', 'Employee role updated successfully.');
    }

    public function updateSupervisor(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required',
        ]);

        $employee->update([
            'supervisor_id' => $validated['supervisor_id'],
        ]);

        notify(
            $employee->user,
            "Supervisor changed",
            "Your supervisor has been changed.",
            route('employees.show', $employee->id),
            'profile'
        );

        return redirect()->back()
            ->with('success', 'Employee supervisor updated successfully.');
    }

    public function updateBatch(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'batch_id' => 'required|exists:batches,id',
        ]);

        $batchId = $validated['batch_id'];

        // Since each employee can only belong to one batch,
        // we simply use sync() to replace the existing one.
        $employee->batches()->sync([$batchId]);

        notify(
            $employee->user,
            "Batch changed",
            "Your batch has been changed.",
            route('employees.show', $employee->id),
            'profile'
        );

        return back()->with('success', 'Employee batch updated successfully.');
    }


    public function uploadProfilePicture(Request $request, Employee $employee)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('profile_picture')) {
            // delete old
            if ($employee->profile_picture && Storage::disk('public')->exists($employee->profile_picture)) {
                Storage::disk('public')->delete($employee->profile_picture);
            }

            // store new on public disk
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');

            $employee->update(['profile_picture' => $path]);

            // return flash containing the new storage path (relative to storage/app/public)
            return redirect()->back()
                ->with('success', 'Profile picture updated successfully.')
                ->with('profile_picture', $path);
        }

        return redirect()->back()->with('error', 'No file uploaded.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }

    private function averageTime($records, $field)
    {
        $minutes = $records
            ->pluck($field)
            ->filter()
            ->map(function ($time) {
                $t = Carbon::parse($time);
                return $t->hour * 60 + $t->minute;
            });

        if ($minutes->isEmpty()) {
            return null;
        }

        $avgMinutes = $minutes->avg();

        return round($avgMinutes / 60, 2);
    }
}
