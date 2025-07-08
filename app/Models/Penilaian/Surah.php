<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surah extends Model
{
    use HasFactory;
    protected $table = "report_surah";
    protected $guarded = [];

    public function tahfidz()
    {
        return $this->belongsTo('App\Models\Penilaian\Tahfidz', 'report_tahfidz_id');
    }

    public function juz()
    {
        return $this->belongsTo('App\Models\Alquran\Juz', 'juz_id');
    }

    public function surat()
    {
        return $this->belongsTo('App\Models\Alquran\Surat', 'surah_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\Alquran\StatusHafalan', 'status_id');
    }
}
