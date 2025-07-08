<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiFormatif extends Model
{
    use HasFactory;

    protected $table = "rkd_formative_scores";
    protected $fillable = [
        'rkd_score_id',
        'index',
		'score'
    ];

    public function nilaiAkhir()
    {
        return $this->belongsTo('App\Models\Penilaian\Kurdeka\NilaiAkhir', 'rkd_score_id');
    }

    public function getCodeAttribute()
    {
        return 'PF-'.$this->index;
    }

    public function getScoreWithSeparatorAttribute()
    {
        return number_format((float)$this->score, 0, ',', '');
    }
}
