<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationCriteria extends Model
{
    protected $fillable = ['name', 'type'];

    public function scores()
    {
        return $this->hasMany(EvaluationScore::class, 'criteria_id');
    }
}
