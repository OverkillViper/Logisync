<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class Batch extends Model
{
    protected $fillable = [
        'name',
    ];

    public function trainees()
    {
        return $this->hasMany(Employee::class)->where('role', 'trainee');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function getTrainersAttribute()
    {
        $traineeIds = $this->trainees()->pluck('employees.id');

        return Employee::where('role', 'trainer')
            ->whereHas('reviewingProjects', function ($query) use ($traineeIds) {
                $query->whereNull('completion_date')
                    ->whereHas('assignees', function ($sub) use ($traineeIds) {
                        $sub->whereIn('employees.id', $traineeIds);
                    });
            })
            ->distinct()
            ->get();
    }
}
