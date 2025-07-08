<?php

namespace App\Models\Apby;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApbyDetail extends Model
{
    use HasFactory;

    protected $table = "apby_detail";
    protected $fillable = [
    	'apby_id',
    	'account_id',
    	'value',
    	'value_rkat',
        'value_faspv',
        'value_fam',
        'value_director',
        'value_president',
    	'used',
        'balance',
    	'employee_id',
    	'edited_employee_id',
    	'edited_status_id',
        'finance_acc_id',
        'finance_acc_status_id',
        'finance_acc_time',
        'director_acc_id',
        'director_acc_status_id',
        'director_acc_time',
        'president_acc_id',
        'president_acc_status_id',
        'president_acc_time',
        'kso_director_acc_id',
        'kso_director_acc_status_id',
        'kso_director_acc_time',
    ];

    public function apby()
    {
        return $this->belongsTo('App\Models\Apby\Apby','apby_id');
    }

    public function akun()
    {
        return $this->belongsTo('App\Models\Anggaran\Akun','account_id')->withTrashed();
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function editPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','edited_employee_id');
    }

    public function editStatus()
    {
        return $this->belongsTo('App\Models\StatusAktif','edited_status_id');
    }

    public function accKeuangan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','finance_acc_id');
    }

    public function accKeuanganStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','finance_acc_status_id');
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

    public function accDirekturKso()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','kso_director_acc_id');
    }

    public function accDirekturKsoStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','kso_director_acc_status_id');
    }

    public function getValueWithSeparatorAttribute()
    {
        return number_format($this->value, 0, ',', '.');
    }

    public function getValueRkatWithSeparatorAttribute()
    {
        return number_format($this->value_rkat, 0, ',', '.');
    }

    public function getValueFaspvWithSeparatorAttribute()
    {
        return number_format($this->value_faspv, 0, ',', '.');
    }

    public function getValueFamWithSeparatorAttribute()
    {
        return number_format($this->value_fam, 0, ',', '.');
    }

    public function getValueDirectorWithSeparatorAttribute()
    {
        return number_format($this->value_director, 0, ',', '.');
    }

    public function getValuePresidentWithSeparatorAttribute()
    {
        return number_format($this->value_president, 0, ',', '.');
    }

    public function getUsedWithSeparatorAttribute()
    {
        return number_format($this->used, 0, ',', '.');
    }

    public function getBalanceWithSeparatorAttribute()
    {
        return number_format($this->balance, 0, ',', '.');
    }
}
