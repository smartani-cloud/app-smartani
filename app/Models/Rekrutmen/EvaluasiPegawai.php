<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluasiPegawai extends Model
{
    use HasFactory;

    protected $table = "tm_evaluation_employee";

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function pscSementara()
    {
        return $this->belongsTo('App\Models\Psc\PscGrade','temp_psc_grade_id');
    }

    public function rekomendasiLanjut()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusRekomendasi','recommend_status_id');
    }

    public function rekomendasiStatus()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPegawai','recommend_employee_status_id');
    }

    public function alasan()
    {
        return $this->belongsTo('App\Models\Phk\AlasanPhk','dismissal_reason_id');
    }

    public function accEdukasi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','education_acc_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','education_acc_status_id');
    }
}
