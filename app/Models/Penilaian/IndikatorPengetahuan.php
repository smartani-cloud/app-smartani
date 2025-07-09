<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndikatorPengetahuan extends Model
{
    use HasFactory;

    protected $table = "report_knowledge_indicator";
    protected $fillable = [
        'semester_id',
        'level_id',
        'subject_id'
    ];
    protected $guarded = [];

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

    public function detail()
    {
        return $this->hasMany('App\Models\Penilaian\IndikatorPengetahuanDetail', 'rki_id');
    }
}
