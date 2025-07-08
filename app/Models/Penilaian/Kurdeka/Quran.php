<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quran extends Model
{
    use HasFactory;
    protected $table = "rkd_qurans";
    protected $fillable = [
        'report_score_id',
		'mem_type_id',
        'order',
        'juz_id',
		'surah_id',
        'verse',
        'status_id',
    ];
	
    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }

    public function jenis()
    {
        return $this->belongsTo('App\Models\Penilaian\HafalanType', 'mem_type_id');
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
