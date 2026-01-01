<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTrackingStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'order',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function entries()
    {
        return $this->hasMany(ProjectTrackingEntry::class);
    }

    public function expectedEntries()
    {
        return $this->entries()->where('type', 'expected');
    }

    public function actualEntries()
    {
        return $this->entries()->where('type', 'actual');
}
}
