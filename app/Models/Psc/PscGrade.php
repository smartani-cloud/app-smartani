<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGrade extends Model
{
    use HasFactory;

    protected $table = "tref_psc_grade";
    protected $fillable = ['set_id','name','start','end'];

    public function set()
    {
        return $this->belongsTo('App\Models\Psc\PscGradeSet','set_id');
    }

    public function evaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','temp_psc_grade_id');
    }

    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','grade_id');
    }
}
