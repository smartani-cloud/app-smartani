<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspekPerkembangan extends Model
{
    use HasFactory;

    protected $table = "tm_development_aspect";
    protected $fillable = [
        'dev_aspect',
        'curriculum_id',
        'is_deleted'
    ];

    public function kurikulum()
    {
        return $this->belongsTo('App\Models\Kbm\Kurikulum','curriculum_id');
    }

    public function indikator()
    {
        return $this->hasMany('App\Models\Penilaian\IndikatorAspek', 'development_aspect_id');
    }

    public function objectives()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\ObjectiveElement', 'element_id');
    }

    public function predicates()
    {
        return $this->hasMany('App\Models\Penilaian\PredikatDeskripsi', 'subject_id');
    }

    public function scopeAktif($query){
    	$query->where('is_deleted',0);
    }
}
