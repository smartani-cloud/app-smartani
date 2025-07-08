<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operational extends Model
{
    use HasFactory;

    protected $table = "tm_operationals";
    protected $fillable = ['name','unit_id'];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project\ProjectOperational','operational_id');
    }
}
