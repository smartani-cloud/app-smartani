<?php

namespace App\Models\Phk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Phk extends Model
{
    use HasFactory;

    protected $table = "tm_dismissal";

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function alasan()
    {
        return $this->belongsTo('App\Models\Phk\AlasanPhk','dismissal_reason_id');
    }

    public function accDirektur()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','director_acc_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','director_acc_status_id');
    }
}
