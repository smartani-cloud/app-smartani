<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPegawai extends Model
{
    use HasFactory;

    protected $table = "tref_employee_status";

    public function kategori()
    {
        return $this->belongsTo('App\Models\Rekrutmen\KategoriPegawai','category_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','status_id');
    }

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','employee_status_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','employee_status_id');
    }

    public function evaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','recommend_employee_status_id');
    }

    public function sppDeduction()
    {
        return $this->hasMany('App\Models\Pembayaran\SppDeduction','employee_status_id');
    }

    public function getAcronymAttribute()
    {
        $words = explode(" ", $this->status);
        $acronym = "";

        foreach ($words as $w) {
          $acronym .= $w[0];
        }

        return $acronym;
    }

    public function scopePegawaiAktif($query)
    {
        return $query->where('status_id', 1);
    }

    public function scopePegawaiTidakTetap($query)
    {
        return $query->where('code', 'like', '02.%');
    }

    public function scopeMitra($query)
    {
        return $query->whereHas('kategori',function($q){
            $q->where('name','Mitra');
        });
    }

    public function scopeStatusCalonPegawai($query)
    {
        return $query->where('code', 'like', '02.%')->orWhere('code', 'like', '03.%');
    }
}
