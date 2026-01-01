<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationScore extends Model
{
    protected $fillable = [
        'evaluation_id', 'criteria_id', 'trainee_id', 'score'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function criteria()
    {
        return $this->belongsTo(EvaluationCriteria::class);
    }

    public function trainee()
    {
        return $this->belongsTo(Employee::class, 'trainee_id');
    }
}
