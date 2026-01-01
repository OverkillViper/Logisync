<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTrackingEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_tracking_stage_id',
        'date',
        'value',
        'type',
    ];

    public function stage()
    {
        return $this->belongsTo(ProjectTrackingStage::class, 'project_tracking_stage_id');
    }
}
