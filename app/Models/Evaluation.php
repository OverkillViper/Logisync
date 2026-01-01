<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'batch_id', 'title', 'start_date', 'end_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function scores()
    {
        return $this->hasMany(EvaluationScore::class);
    }

    public function remarks()
    {
        return $this->hasMany(EvaluationRemarks::class);
    }
}