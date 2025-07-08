<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisKelamin extends Model
{
    use HasFactory;

    protected $table = "tref_gender";

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','gender_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','gender_id');
    }
    
    public function siswa()
    {
        return $this->hasMany('App\Models\Siswa\Siswa');
    }
}
