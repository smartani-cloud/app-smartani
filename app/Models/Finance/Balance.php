<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    protected $table = "tm_balances";
    protected $fillable = [
    	'sof_id',
        'balance'
    ];

    public function sof()
    {
        return $this->belongsTo('App\Models\Project\SoF','sof_id');
    }

    public function mutations()
    {
        return $this->hasMany('App\Models\Finance\BalanceMutation','balance_id');
    }   
	
    public function getBalanceWithSeparatorAttribute()
    {
        return number_format($this->balance, 0, ',', '.');
    }
}
