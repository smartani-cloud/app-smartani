<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectOperational extends Model
{
    use HasFactory;

    protected $table = "project_operational";
    protected $fillable = [
    	'project_id',
    	'operational_id',
    	'operational_desc',
        'nominal',
        'percentage'
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function operational()
    {
        return $this->belongsTo('App\Models\Project\Operational','operational_id');
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }
}
