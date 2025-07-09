<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscScore extends Model
{
    use HasFactory;

    protected $table = "psc_score";
    protected $fillable = [
    	'unit_id',
    	'position_id',
    	'position_name',
    	'academic_year_id',
    	'employee_id',
    	'employee_name',
    	'total_score',
    	'grade_id',
    	'grade_name',
		'psc_grade_record_id',
		'validator_id',
    	'acc_employee_id',
    	'acc_status_id',
    	'acc_time'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function grade()
    {
        return $this->belongsTo('App\Models\Psc\PscGrade','grade_id');
    }
	
	public function gradeRecord()
    {
        return $this->belongsTo('App\Models\Psc\PscGradeRecord','psc_grade_record_id');
    }
	
	public function validator()
    {
        return $this->belongsTo('App\Models\Psc\PscValidator','validator_id');
    }

    public function accPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','acc_employee_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','acc_status_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Psc\PscScoreIndicator','psc_score_id');
    }
}
