<?php

namespace App\Models\Phk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlasanPhk extends Model
{
    use HasFactory;

    protected $table = "tref_dismissal_reason";

    public function evaluasi()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','dismissal_reason_id');
    }

    public function phk()
    {
        return $this->hasMany('App\Models\Phk\Phk','dismissal_reason_id');
    }
}
