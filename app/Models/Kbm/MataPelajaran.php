<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;
    protected $table = "tm_subjects";
    protected $fillable = [
        'subject_number', 
        'subject_name',
        'subject_code', 
        'subject_acronym',
        'group_subject_id',
        'kkm',
        'unit_id',
        'is_mulok'
    ];

    public function kmps()
    {
        return $this->belongsTo('App\Models\Kbm\KelompokMataPelajaran','group_subject_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function skbmDetail()
    {
        return $this->hasMany('App\Models\Skbm\SkbmDetail','subject_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany('App\Models\Kbm\JadwalPelajaran','subject_id');
    }

    public function sikap()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiSikapPts', 'subject_id');
    }

    public function mapelKelas()
    {
        return $this->hasMany('App\Models\Kbm\MapelKelas','subject_id');
    }

    public function kkm()
    {
        return $this->hasMany('App\Models\Kbm\KkmPelajaran','subject_id');
    }

    public function kd()
    {
        return $this->hasMany('App\Models\Penilaian\KDSetting','subject_id');
    }

    public function rangePredikat()
    {
        return $this->hasMany('App\Models\Penilaian\RangePredikat', 'subject_id');
    }

    public function predicate()
    {
        return $this->hasMany('App\Models\Penilaian\PredikatDeskripsi', 'subject_id');
    }
	
	public function indikatorPengetahuan()
    {
        return $this->hasMany('App\Models\Penilaian\IndikatorPengetahuan', 'subject_id');
    }
	
	public function tpsDescs()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\TpsDesc', 'subject_id');
    }

    public function nilaiAkhirKurdeka()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\NilaiAkhir', 'subject_id');
    }
    
    public function finalScorePercentages()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\PersentaseNilaiAkhir', 'subject_id');
    }

    public function getAcronymAttribute()
    {
        $words = explode(" ", $this->subject_name);
        $acronym = "";

        foreach ($words as $w) {
            if(in_array($w,['Matematika','Indonesia','Inggris'])){
                $acronym .= strtoupper(substr($w,0,3));
            }
            else{
                if($w[0] != 'd')
                    $acronym .= $w[0];
            }
        }

        return $acronym;
    }

    public function scopeMulok($query){
        return $query->where('is_mulok',1);
    }
}
