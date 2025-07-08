<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectStatus extends Model
{
    use HasFactory;

    protected $table = "tref_project_statuses";
    protected $fillable = ['name'];

    public function projects()
    {
        return $this->hasMany('App\Models\Project\Project','status_id');
    }
}
