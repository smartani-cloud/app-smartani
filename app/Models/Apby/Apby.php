<?php

namespace App\Models\Apby;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apby extends Model
{
    use HasFactory;

    protected $table = "apby";
    protected $fillable = [
    	'year',
        'academic_year_id',
    	'budgeting_budgeting_type_id',
    	'total_value',
        'total_used',
    	'total_balance',
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
        'revision',
        'is_active',
        'is_final',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function jenisAnggaranAnggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_budgeting_type_id');
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

    public function detail()
    {
        return $this->hasMany('App\Models\Apby\ApbyDetail','apby_id');
    }

    public function transferLogs()
    {
        return $this->hasMany('App\Models\Apby\ApbyTransferLog','apby_id');
    }

    public function ppa()
    {
        return $this->hasMany('App\Models\Ppa\Ppa','apby_id');
    }

    public function getTotalValueWithSeparatorAttribute()
    {
        return number_format($this->total_value, 0, ',', '.');
    }

    public function getTotalUsedWithSeparatorAttribute()
    {
        return number_format($this->total_used, 0, ',', '.');
    }

    public function getTotalBalanceWithSeparatorAttribute()
    {
        return number_format($this->total_balance, 0, ',', '.');
    }
    
    public function scopeAktif($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeFinal($query)
    {
        return $query->where('is_final',1);
    }
    
    public function scopeUnfinal($query)
    {
        return $query->where('is_final',0);
    }
}
