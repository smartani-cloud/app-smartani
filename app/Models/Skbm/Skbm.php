<?php

namespace App\Models\Skbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skbm extends Model
{
    use HasFactory;

    protected $table = "skbm";

    public function tahunAjaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran', 'academic_year_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }

    public function kepalaSekolah()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai', 'principle_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusArsip', 'status_id');
    }

    public function arsip()
    {
        return $this->hasMany('App\Models\Skbm\SkbmArsip', 'skbm_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Skbm\SkbmDetail', 'skbm_id');
    }

    public function getShowAttribute()
    {
        //if ($this->status->status == 'aktif') return $this->detail;
        //else return $this->arsip;
        return $this->detail;
    }

    public function scopeAktif($query)
    {
        return $query->where('status_id', 1);
    }
}
