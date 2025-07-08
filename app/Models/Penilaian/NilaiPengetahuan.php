<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPengetahuan extends Model
{
    use HasFactory;

    protected $table = "score_knowledge";
    protected $guarded = [];

    public function nilaipengetahuandetail()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiPengetahuanDetail', 'score_knowledge_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran', 'subject_id');
    }

    public function deskripsi()
    {
        return $this->belongsTo('App\Models\Penilaian\PredikatDeskripsi', 'rpd_id');
    }

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }
}
