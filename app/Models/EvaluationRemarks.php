<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationRemarks extends Model
{
    protected $fillable = [
        'evaluation_id',
        'trainee_id',
        'remarks',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function trainee()
    {
        return $this->belongsTo(Employee::class, 'trainee_id');
    }
}
