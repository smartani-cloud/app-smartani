<?php

namespace App\Models\Lppa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Lppa extends Model
{
    use HasFactory;

    protected $table = "lppa";
    protected $fillable = [
    	'number',
        'ppa_id',
    	'date',
    	'difference_total_value',
    	'finance_acc_id',
    	'finance_acc_status_id',
    	'finance_acc_time'
    ];

    public function ppa()
    {
        return $this->belongsTo('App\Models\Ppa\Ppa','ppa_id');
    }

    public function accKeuangan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','finance_acc_id');
    }

    public function accKeuanganStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','finance_acc_status_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Lppa\LppaDetail','lppa_id');
    }

    public function ppaKurang()
    {
        return $this->hasOne('App\Models\Ppa\Ppa','lppa_id');
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

    public function getDifferenceTotalValueWithSeparatorAttribute()
    {
        return number_format($this->difference_total_value, 0, ',', '.');
    }
}
