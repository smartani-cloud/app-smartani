<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersentaseNilaiAkhir extends Model
{
    use HasFactory;
    protected $table = "rkd_final_score_percentages";
    protected $fillable = [
        'semester_id',
        'level_id',
		'subject_id',
		'naf_percentage',
		'nas_percentage',
		'ntss_percentage',
		'nass_percentage'
    ];
    protected $dates = ['deleted_at'];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran','subject_id');
	}
}
