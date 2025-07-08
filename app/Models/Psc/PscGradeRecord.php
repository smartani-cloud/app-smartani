<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGradeRecord extends Model
{
    use HasFactory;

    protected $table = "tref_psc_grade_records";
    protected $fillable = ['grades'];
	
    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','psc_grade_record_id');
    }
}
