<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;
    protected $table = "tref_academic_year";
    protected $fillable = ['academic_year','academic_year_start','academic_year_end','is_active','is_finance_year'];

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','is_active');
    }

    public function semester()
    {
        return $this->hasMany('App\Models\Kbm\Semester','academic_year_id');
    }

    public function penempatanPegawai()
    {
        return $this->hasMany('App\Models\Penempatan\PenempatanPegawai','academic_year_id');
    }

    public function skbm()
    {
        return $this->hasMany('App\Models\Skbm\Skbm','academic_year_id');
    }

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','academic_year_id');
    }

    public function nilaiPsc()
    {
        return $this->hasMany('App\Models\Psc\PscScore','academic_year_id');
    }

    public function ikuAspekUnit()
    {
        return $this->hasMany('App\Models\Iku\IkuAspectUnit','academic_year_id');
    }

    public function nilaiIku()
    {
        return $this->hasMany('App\Models\Iku\IkuAchievement','academic_year_id');
    }

    public function kelas()
    {
        return $this->hasMany('App\Models\Kbm\Kelas','academic_year_id');
    }
    
    public function sertifIklas()
    {
        return $this->hasMany('App\Models\Penilaian\SertifIklas','academic_year_id');
    }

    public function jenisAnggarans()
    {
        return $this->hasMany('App\Models\Anggaran\jenisAnggaranAnggaranRiwayat','academic_year_id');
    }

    public function rkat()
    {
        return $this->hasMany('App\Models\Rkat\Rkat','academic_year_id');
    }

    public function apby()
    {
        return $this->hasMany('App\Models\Apby\Apby','academic_year_id');
    }

    public function proposalPpas()
    {
        return $this->hasMany('App\Models\Ppa\PpaProposal','academic_year_id');
    }

    public function ppa()
    {
        return $this->hasMany('App\Models\Ppa\Ppa','academic_year_id');
    }

    public function bbk()
    {
        return $this->hasMany('App\Models\Bbk\Bbk','academic_year_id');
    }

    public function psbRegisterCounter()
    {
        return $this->hasMany('App\Models\Psb\RegisterCounter','academic_year_id');
    }

    public function bmsTermin()
    {
        return $this->hasMany('App\Models\Pembayaran\BmsTermin','academic_year_id');
    }

    public function getAcademicYearLinkAttribute()
    {
        return str_replace("/","-",$this->academic_year);
    }

    public function scopeAktif($query){
        return $query->where('is_active',1);
    }

    public function scopeFinanceYear($query){
        return $query->where('is_finance_year',1);
    }
}
