<?php

namespace App\Models\Skbm;;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class SkbmDetail extends Model
{
    use HasFactory;

    protected $table = "skbm_detail";

    public function skbm()
    {
        return $this->belongsTo('App\Models\Skbm\Skbm','skbm_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran','subject_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function getTeachingDecreeDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->teaching_decree_date)->format('j F Y');
    }
}
