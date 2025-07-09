<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGrade extends Model
{
    use HasFactory;

    protected $table = "tref_psc_grade";

    public function pscGradeSet()
    {
        return $this->belongsTo('App\Models\Penilaian\PscGradeSet','set_id');
    }

    public function evaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','temp_psc_grade_id');
    }
}
