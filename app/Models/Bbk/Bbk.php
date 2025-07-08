<?php

namespace App\Models\Bbk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Bbk extends Model
{
    use HasFactory;

    protected $table = "bbk";
    protected $fillable = [
    	'number',
    	'date',
        'year',
        'academic_year_id',
        'budgeting_type_id',
    	'total_value',
    	'employee_id',
    	'director_acc_id',
    	'director_acc_status_id',
    	'director_acc_time',
    	'president_acc_id',
    	'president_acc_status_id',
    	'president_acc_time',
    	'disbursement_status_id',
    	'disbursement_time',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function jenisAnggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaran','budgeting_type_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function accDirektur()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','director_acc_id');
    }

    public function accDirekturStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','director_acc_status_id');
    }

    public function accKetua()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','president_acc_id');
    }

    public function accKetuaStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','president_acc_status_id');
    }

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','disbursement_status_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Bbk\BbkDetail','bbk_id');
    }

    public function getFirstNumberAttribute(){
        return $this->number ? (int)explode('/',$this->number)[0] : null;
    }

    public function getNumberAsNameAttribute(){
        return $this->number ? str_replace('/','-',$this->number) : null;
    }

    public function getNumberOnlyAttribute(){
        return $this->number ? implode('-',explode('/',$this->number,-1)) : null;
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('j F Y');
    }

    public function getTotalValueWithSeparatorAttribute()
    {
        return number_format($this->total_value, 0, ',', '.');
    }

    public function getDirectorAccDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->director_acc_time)->format('j F Y');
    }

    public function getPresidentAccDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->president_acc_time)->format('j F Y');
    }

    public function getDisbursementDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->disbursement_time)->format('j F Y');
    }
}
