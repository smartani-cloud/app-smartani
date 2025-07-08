<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusAktif extends Model
{
    use HasFactory;

    protected $table = "tref_active_status";

    public function statusBaru()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','join_badge_status_id');
    }

    public function statusPhk()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','disjoin_badge_status_id');
    }

    public function statusPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','active_status_id');
    }

    public function statusSpk()
    {
        return $this->hasMany('App\Models\Rekrutmen\Spk','status_id');
    }

    public function statusPscGradeSet()
    {
        return $this->hasMany('App\Models\Penilaian\PscGradeSet','status_id');
    }

    public function tahunAjaran()
    {
        return $this->hasMany('App\Models\Kbm\TahunAjaran','is_active');
    }

    public function semester()
    {
        return $this->hasMany('App\Models\Kbm\Semester','is_active');
    }
}
