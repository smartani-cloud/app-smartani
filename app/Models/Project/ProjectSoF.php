<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectSoF extends Model
{
    use HasFactory;

    protected $table = "project_sof";
    protected $fillable = [
    	'project_id',
    	'sof_id',
        'nominal',
        'percentage'
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function sof()
    {
        return $this->belongsTo('App\Models\Project\SoF','sof_id');
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }
}
