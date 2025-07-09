<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelPhase extends Model
{
    use HasFactory;
    protected $table = "tref_level_phases";
    protected $fillable = [
		'name'
    ];
    
    public function levels()
    {
        return $this->hasMany('App\Models\Level', 'phase_id');
    }
}
