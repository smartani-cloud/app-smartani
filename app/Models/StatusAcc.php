<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusAcc extends Model
{
    use HasFactory;

    protected $table = "tref_acc_status";

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','education_acc_status_id');
    }

    public function penempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawaiDetail','acc_status_id');
    }

    public function evaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','education_acc_status_id');
    }

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','education_acc_status_id');
    }

    public function phk()
    {
        return $this->hasMany('App\Models\Phk\Phk','director_acc_status_id');
    }
}
