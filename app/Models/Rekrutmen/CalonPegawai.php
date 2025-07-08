<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use File;
use Carbon\Carbon;

class CalonPegawai extends Model
{
    use HasFactory;

    protected $table = "tm_candidate_employees";

    public function units()
    {
        return $this->belongsToMany('App\Models\Unit','candidate_employee_unit','candidate_employee_id', 'unit_id')->withTimestamps();
    }

    public function jabatans()
    {
        return $this->belongsToMany('App\Models\Penempatan\JabatanUnit','candidate_employee_position','candidate_employee_id', 'position_id')->withTimestamps();
    }

    public function jenisKelamin()
    {
        return $this->belongsTo('App\Models\JenisKelamin','gender_id');
    }

    public function statusPernikahan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPernikahan','marriage_status_id');
    }

    public function alamat()
    {
        return $this->belongsTo('App\Models\Wilayah','region_id');
    }

    public function pendidikanTerakhir()
    {
        return $this->belongsTo('App\Models\Rekrutmen\PendidikanTerakhir','recent_education_id');
    }

    public function latarBidangStudi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\LatarBidangStudi','academic_background_id');
    }

    public function universitas()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Universitas','university_id');
    }

    public function rekomendasiPenerimaan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPenerimaan','acceptance_status_id');
    }

    public function rekomendasiJabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }

    public function rekomendasiPenempatan()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function statusPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\StatusPegawai','employee_status_id');
    }

    public function accEdukasi()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','education_acc_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','education_acc_status_id');
    }

    public function getBirthDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->birth_date)->format('j F Y');
    }

    public function getAgeAttribute()
    {
        return $this->ageOriginal.' tahun';
    }

    public function getAgeOriginalAttribute()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function getPhotoPathAttribute()
    {
        if($this->photo) return 'img/photo/calon/'.$this->photo;
        else return null;
    }

    public function getShowPhotoAttribute(){
        return File::exists($this->photoPath) ? $this->photoPath : 'img/avatar/default.png';
    }

    public function getRegionCodeAttribute()
    {
        return $this->alamat->code;
    }

    public function getPeriodIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->period_start)->format('j F Y').' s.d. '.Carbon::parse($this->period_end)->format('j F Y');
    }
}
