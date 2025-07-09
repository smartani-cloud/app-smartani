<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    protected $table = "tm_semester";
    protected $fillable = ['semester_id','semester','academic_year_id','is_active'];

    public function tahunAjaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','is_active');
    }

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','semester_id');
    }

    public function siswa()
    {
        return $this->hasMany('App\Models\Siswa\Siswa');
    }

    public function kkm()
    {
        return $this->hasMany('App\Models\Kbm\KkmPelajaran','semester_id');
    }
    
    public function riwayatKelas()
    {
        return $this->hasMany('App\Models\Kbm\HistoryKelas','semester_id');
    }
    
    public function jadwalPelajarans()
    {
        return $this->hasMany('App\Models\Kbm\JadwalPelajaran','semester_id');
    }
	
	public function indikatorPengetahuan()
    {
        return $this->hasMany('App\Models\Penilaian\IndikatorPengetahuan', 'semester_id');
    }

    public function tanggalRapor()
    {
        return $this->hasMany('App\Models\Penilaian\TanggalRapor','semester_id');
    }

    public function curricula()
    {
        return $this->hasMany('App\Models\Kbm\TingkatKurikulum','semester_id');
    }

    public function khatamTypes()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\LevelKhatamType','semester_id');
    }

    public function books()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\UnitBuku','semester_id');
    }

    public function kompetensiKategoriIklas()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\KompetensiKategoriIklas','semester_id');
    }

    public function objectiveElements()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\ObjectiveElement','semester_id');
    }
    
    public function getSemesterNumberAttribute()
    {
        return explode("-",$this->semester_id)[1];
    }

    public function getSemesterLinkAttribute()
    {
        return str_replace("/","-",$this->tahunAjaran->academic_year).'/'.$this->semesterNumber;
    }

    public function scopeAktif($query){
        return $query->where('is_active',1);
    }
}
