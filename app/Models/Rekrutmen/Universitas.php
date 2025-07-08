<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Universitas extends Model
{
    use HasFactory;

    protected $table = "tref_university";

    protected $fillable = ['name'];

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','university_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','university_id');
    }
}
