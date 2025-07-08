<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAkhir extends Model
{
    use HasFactory;

    protected $table = "rkd_scores";
    protected $fillable = [
        'report_score_id',
        'subject_id',
		'project',
		'naf',
		'nas',
		'ntss',
		'nass',
		'nar'
    ];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran', 'subject_id');
    }
	
    public function nilaiFormatif()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\NilaiFormatif', 'rkd_score_id');
    }
	
    public function nilaiSumatif()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\NilaiSumatif', 'rkd_score_id');
    }

    public function getProjectWithSeparatorAttribute()
    {
        return number_format((float)$this->project, 0, ',', '');
    }

    public function getNafWithSeparatorAttribute()
    {
        return number_format((float)$this->naf, 0, ',', '');
    }

    public function getNasWithSeparatorAttribute()
    {
        return number_format((float)$this->nas, 0, ',', '');
    }

    public function getNtssWithSeparatorAttribute()
    {
        return number_format((float)$this->ntss, 0, ',', '');
    }

    public function getNassWithSeparatorAttribute()
    {
        return number_format((float)$this->nass, 0, ',', '');
    }

    public function getNarWithSeparatorAttribute()
    {
        return number_format((float)$this->nar, 0, ',', '');
    }
}
