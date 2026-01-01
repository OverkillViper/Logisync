<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'punch_in',
        'punch_out',
        'on_leave',
        'holiday',
    ];

    protected $casts = [
        'date'      => 'date',
        'punch_in'  => 'datetime',
        'punch_out' => 'datetime',
        'on_leave'  => 'boolean',
        'holiday'   => 'boolean',
    ];

    // Optional relation
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', now()->toDateString());
    }
}
