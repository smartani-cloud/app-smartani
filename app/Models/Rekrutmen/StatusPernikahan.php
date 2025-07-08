<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPernikahan extends Model
{
    use HasFactory;

    protected $table = "tref_marriage_status";

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','marriage_status_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','marriage_status_id');
    }
}
