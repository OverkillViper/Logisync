<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;
use App\Models\ProjectComment;
use App\Models\ProjectTracking;
use App\Models\User;
use Carbon\Carbon;

class Project extends Model
{

    protected $fillable = [
        'name',
        'start_date',
        'deadline',
        'initial_deadline',
        'completion_date',
        'created_by',
    ];

    protected $appends = ['status', 'remaining_days'];

    public function getStatusAttribute()
    {
        $now = Carbon::today();
        $start = $this->start_date ? Carbon::parse($this->start_date) : null;
        $deadline = $this->deadline ? Carbon::parse($this->deadline) : null;
        $completion = $this->completion_date ? Carbon::parse($this->completion_date) : null;

        if ($completion) {
            return $completion->lte($deadline)
                ? 'Completed'
                : 'Completed Late';
        }

        if ($start && $now->lt($start)) {
            return 'Not Started';
        }

        if ($deadline && $now->gt($deadline)) {
            return 'Overdue';
        }

        return 'In Progress';
    }

    public function assignees()
    {
        return $this->belongsToMany(
            Employee::class,
            'project_assignee',
            'project_id',
            'employee_id'
        )->withTimestamps();
    }

    public function reviewers()
    {
        return $this->belongsToMany(
            Employee::class,
            'project_reviewer',
            'project_id',
            'employee_id'
        )->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(ProjectComment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getRemainingDaysAttribute()
    {
        if (!$this->deadline) {
            return null; // no deadline means no remaining days
        }

        $now = Carbon::today();
        $deadline = Carbon::parse($this->deadline);

        // If project completed, calculate days taken instead
        if ($this->completion_date) {
            $completion = Carbon::parse($this->completion_date);
            return $completion->diffInDays($deadline, false); // negative if late
        }

        // Otherwise, days remaining till deadline
        return (int) $now->diffInDays($deadline, false); // negative if overdue
    }

    public function deadlineExtensions()
    {
        return $this->hasMany(DeadlineExtension::class);
    }

    public function completionRequest()
    {
        return $this->hasMany(ProjectCompletionRequest::class);
    }

    public function trackingStages()
    {
        return $this->hasMany(ProjectTrackingStage::class);
    }
}
