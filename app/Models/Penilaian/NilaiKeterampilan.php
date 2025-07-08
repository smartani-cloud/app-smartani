<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiKeterampilan extends Model
{
    use HasFactory;

    protected $table = "score_skill";
    protected $guarded = [];

    public function nilaiketerampilandetail()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiKeterampilanDetail', 'score_skill_id');
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
