<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class BalanceMutation extends Model
{
    use HasFactory;

    protected $table = "tm_balance_mutations";
    protected $fillable = [
    	'balance_id',
        'type_id',
    	'date',
    	'desc',
        'amount',
        'balance'
    ];

    public function balance()
    {
        return $this->belongsTo('App\Models\Finance\Balance','balance_id');
    }

    public function type()
    {
        return $this->belongsTo('App\Models\Finance\FinanceType','type_id');
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('j F Y');
    }

    public function getAmountWithSeparatorAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }

    public function getBalanceWithSeparatorAttribute()
    {
        return number_format($this->balance, 0, ',', '.');
    }
    
    public function scopeIncome($query)
    {
        return $query->where('type_id',2);
    }
    
    public function scopeOutcome($query)
    {
        return $query->whereIn('type_id',[1,3]);
    }
}
