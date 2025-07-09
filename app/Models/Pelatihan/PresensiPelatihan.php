<?php

namespace App\Models\Pelatihan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiPelatihan extends Model
{
    use HasFactory;

    protected $table = "training_presence";

    public function pelatihan()
    {
        return $this->belongsTo('App\Models\Pelatihan\Pelatihan','training_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function accEdukasi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','education_acc_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Pelatihan\StatusPresensi','presence_status_id');
    }
}
