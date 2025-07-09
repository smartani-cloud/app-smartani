<?php

namespace App\Models\Rkat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RkatDetail extends Model
{
    use HasFactory;

    protected $table = "rkat_detail";
    protected $fillable = [
    	'rkat_id',
    	'account_id',
    	'value',
    	'value_pa',
        'value_fam',
        'value_director',
    	'employee_id',
    	'edited_employee_id',
    	'edited_status_id',
        'finance_acc_id',
        'finance_acc_status_id',
        'finance_acc_time',
        'director_acc_id',
        'director_acc_status_id',
        'director_acc_time'
    ];

    public function rkat()
    {
        return $this->belongsTo('App\Models\Rkat\Rkat','rkat_id');
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

    public function getValueWithSeparatorAttribute()
    {
        return number_format($this->value, 0, ',', '.');
    }

    public function getValuePaWithSeparatorAttribute()
    {
        return number_format($this->value_pa, 0, ',', '.');
    }

    public function getValueFamWithSeparatorAttribute()
    {
        return number_format($this->value_fam, 0, ',', '.');
    }

    public function getValueDirectorWithSeparatorAttribute()
    {
        return number_format($this->value_director, 0, ',', '.');
    }
}
