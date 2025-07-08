<?php

namespace App\Models;

use App\Models\Penempatan\PenempatanPegawai;
use App\Models\Rekrutmen\Pegawai;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $table = "tref_unit";

    public function jabatan()
    {
        return $this->belongsToMany('App\Models\Penempatan\Jabatan','tm_position_unit','unit_id', 'position_id')->withTimestamps();
    }

    public function calonPegawais()
    {
        return $this->belongsToMany('App\Models\Rekrutmen\CalonPegawai','candidate_employee_unit','unit_id', 'candidate_employee_id')->withTimestamps();
    }

    public function greenhouseOwners()
    {
        return $this->belongsToMany('Modules\FarmManagement\Models\GreenhouseOwner','tas_greenhouse_owner_units','unit_id','owner_id')->withTimestamps();
    }    

    public function greenhouse()
    {
        return $this->hasOne('Modules\FarmManagement\Models\Greenhouse','unit_id');
    }

    public function pegawais()
    {
        return $this->hasMany('App\Models\Rekrutmen\PegawaiUnit','unit_id');
    }

    public function region()
    {
        return $this->belongsTo('Modules\Core\Models\References\Region','region_id');
    }

    public function wilayah()
    {
        return $this->belongsTo('App\Models\Wilayah','region_id');
    }

    public function penempatanPegawai()
    {
        return $this->hasMany(PenempatanPegawai::class,'unit_id');
    }

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','unit_id');
    }

    public function pegawai()
    {
        return $this->hasMany(Pegawai::class,'unit_id');
    }

    public function skbm()
    {
        return $this->hasMany('App\Models\Skbm\Skbm','unit_id');
    }

    public function pelatihan()
    {
        return $this->hasMany('App\Models\Pelatihan\Pelatihan','organizer_id');
    }

    public function siswa()
    {
        return $this->hasMany('App\Models\Siswa\Siswa','unit_id');
    }
    
    public function mataPelajaran()
    {
        return $this->hasMany('App\Models\Kbm\MataPelajaran','unit_id');
    }

    public function kelompokMataPelajaran()
    {
        return $this->hasMany('App\Models\Kbm\KelompokMataPelajaran','unit_id');
    }

    public function targetTahfidz()
    {
        return $this->hasMany('App\Models\Penilaian\TargetTahfidz','unit_id');
    }

    public function anggaran()
    {
        return $this->hasMany('App\Models\Anggaran\Anggaran','unit_id');
    }

    public function nilaiPsc()
    {
        return $this->hasMany('App\Models\Psc\PscScore','unit_id');
    }

    public function ikuAspek()
    {
        return $this->hasMany('App\Models\Iku\IkuAspectUnit','unit_id');
    }

    public function ikuNilai()
    {
        return $this->hasMany('App\Models\Iku\IkuAchievement','unit_id');
    }

    public function levels()
    {
        return $this->hasMany('App\Models\Level','unit_id');
    }

    public function kompetensiIklas()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\KompetensiIklas','unit_id');
    }

    public function indikatorIklas()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\IndikatorIklas','unit_id');
    }

    public function kompetensiKategoriIklas()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\KompetensiKategoriIklas','unit_id');
    }

    public function books()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\UnitBuku','unit_id');
    }

    public function psbRegisterCounter()
    {
        return $this->hasMany('App\Models\Psb\RegisterCounter','unit_id');
    }

    public function bmsSiswa()
    {
        return $this->hasMany('App\Models\Pembayaran\BMS','unit_id');
    }

    public function bmsCalon()
    {
        return $this->hasMany('App\Models\Pembayaran\BmsCalonSiswa','unit_id');
    }

    public function bmsNominal()
    {
        return $this->hasMany('App\Models\Pembayaran\BmsNominal','unit_id');
    }

    public function spp()
    {
        return $this->hasMany('App\Models\Pembayaran\Spp','unit_id');
    }

    public function sppBill()
    {
        return $this->hasMany('App\Models\Pembayaran\SppBill','unit_id');
    }

    public function sppNominal()
    {
        return $this->hasMany('App\Models\Pembayaran\SppNominal','unit_id');
    }
    
    public function scopeSekolah($query)
    {
        return $query->where('is_school',1);
    }

    public function kepala()
    {
        return $this->hasMany(Pegawai::class,'unit_id')->whereHas('login', function ($q){
            $q->where('role_id',2);
        });
    }
}
