<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahfidz extends Model
{
    use HasFactory;
    protected $table = "report_tahfidz";
    protected $guarded = [];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }

    public function deskripsi()
    {
        return $this->belongsTo('App\Models\Penilaian\PredikatDeskripsi', 'rpd_id');
    }

    public function surah()
    {
        return $this->hasMany('App\Models\Penilaian\Surah', 'report_tahfidz_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Penilaian\Surah', 'report_tahfidz_id');
    }
}
