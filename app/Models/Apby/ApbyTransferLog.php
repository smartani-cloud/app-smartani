<?php

namespace App\Models\Apby;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApbyTransferLog extends Model
{
    use HasFactory;

    protected $table = "apby_transfer_log";
    protected $fillable = [
    	'apby_id',
    	'from_detail_id',
    	'from_value',
        'from_balance',
    	'to_detail_id',
    	'to_value',
        'to_balance',
        'amount',
    	'employee_id'
    ];

    public function apby()
    {
        return $this->belongsTo('App\Models\Apby\Apby','apby_id');
    }

    public function dariDetail()
    {
        return $this->belongsTo('App\Models\Apby\ApbyDetail','from_detail_id');
    }

    public function keDetail()
    {
        return $this->belongsTo('App\Models\Apby\ApbyDetail','to_detail_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function getFromValueWithSeparatorAttribute()
    {
        return number_format($this->from_value, 0, ',', '.');
    }

    public function getFromBalanceWithSeparatorAttribute()
    {
        return number_format($this->from_balance, 0, ',', '.');
    }

    public function getToValueWithSeparatorAttribute()
    {
        return number_format($this->to_value, 0, ',', '.');
    }

    public function getToBalanceWithSeparatorAttribute()
    {
        return number_format($this->to_balance, 0, ',', '.');
    }

    public function getAmountWithSeparatorAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }
}
