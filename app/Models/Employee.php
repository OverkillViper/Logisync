<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Batch;
use App\Models\User;
use App\Models\Project;
use Carbon\Carbon;

class Employee extends Model
{
    protected $fillable = [
        'user_id',
        'employee_id',
        'supervisor_id',
        'profile_picture',
        'joining_date',
        'contact_number',
        'emergency_contact_number',
        'emergency_contact_relation',
        'designation',
        'role',
    ];

    protected $appends = ['experience']; // include it automatically in JSON

    public function getExperienceAttribute()
    {
        if (!$this->join_date) {
            return 'Not set';
        }

        $joinDate = Carbon::parse($this->joining_date);
        $now = Carbon::now();

        $years = $joinDate->diffInYears($now);
        $months = $joinDate->copy()->addYears($years)->diffInMonths($now);

        // You can format however you want:
        if ($years > 0 && $months > 0) {
            return "{$years} yr {$months} mo";
        } elseif ($years > 0) {
            return "{$years} yr";
        } elseif ($months > 0) {
            return "{$months} mo";
        } else {
            return "Less than a month";
        }
    }

    protected $casts = [
        'joining_date' => 'date:d-m-Y',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function assignedProjects()
    {
        return $this->belongsToMany(
            Project::class,                   // Related model
            'project_assignee',                 // Pivot table
            'employee_id',            // Foreign key on pivot table for this model
            'project_id'              // Foreign key on pivot table for related model
        )
        ->withTimestamps();
        // ->whereNull('projects.completion_date');
    }

    public function reviewingProjects()
    {
        return $this->belongsToMany(
            Project::class,                   // Related model
            'project_reviewer',                 // Pivot table
            'employee_id',            // Foreign key on pivot table for this model
            'project_id'              // Foreign key on pivot table for related model
        )
        ->withTimestamps();
        // ->whereNull('projects.completion_date');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    // Optional: employees that this user supervises
    public function subordinates()
    {
        return $this->hasMany(Employee::class, 'supervisor_id');
    }

    public function deadlineExtensionsRequested()
    {
        return $this->hasMany(DeadlineExtension::class, 'requested_by');
    }

    public function deadlineExtensionsApproved()
    {
        return $this->hasMany(DeadlineExtension::class, 'approved_by');
    }

    public function evaluationScores()
    {
        return $this->hasMany(EvaluationScore::class, 'trainee_id');
    }
}
