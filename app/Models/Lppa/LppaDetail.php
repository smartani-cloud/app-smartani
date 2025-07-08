<?php

namespace App\Models\Lppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LppaDetail extends Model
{
    use HasFactory;

    protected $table = "lppa_detail";
    protected $fillable = [
    	'lppa_id',
    	'ppa_detail_id',
    	'value',
    	'receipt_status_id',
    	'employee_id',
    	'edited_employee_id',
    	'edited_status_id',
    	'acc_employee_id',
    	'acc_status_id',
    	'acc_time'
    ];

    public function lppa()
    {
        return $this->belongsTo('App\Models\Lppa\Lppa','lppa_id');
    }

    public function ppaDetail()
    {
        return $this->belongsTo('App\Models\Ppa\PpaDetail','ppa_detail_id');
    }

    public function buktiStatus()
    {
        return $this->belongsTo('App\Models\StatusEksistensi','receipt_status_id');
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

    public function accPegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','acc_employee_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','acc_status_id');
    }

    public function getValueWithSeparatorAttribute()
    {
        return number_format($this->value, 0, ',', '.');
    }
}
