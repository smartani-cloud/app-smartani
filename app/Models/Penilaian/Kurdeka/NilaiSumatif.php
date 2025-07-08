<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSumatif extends Model
{
    use HasFactory;

    protected $table = "rkd_summative_scores";
    protected $fillable = [
        'rkd_score_id',
        'tps_desc_id',
		'score'
    ];

    public function nilaiAkhir()
    {
        return $this->belongsTo('App\Models\Penilaian\Kurdeka\NilaiAkhir', 'rkd_score_id');
    }
	
	public function deskripsi()
    {
        return $this->hasOne('App\Models\Penilaian\Kurdeka\TpsDesc', 'tps_desc_id');
    }

    public function getScoreWithSeparatorAttribute()
    {
        return number_format((float)$this->score, 0, ',', '');
    }
}
