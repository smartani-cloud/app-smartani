<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceUnit extends Model
{
    use HasFactory;

    protected $table = "finance_unit";
    protected $fillable = [
    	'finance_id',
    	'unit_id',
    	'nominal_income',
        'nominal_outcome',
        'nominal_income_real',
        'nominal_outcome_real'
    ];

    public function finance()
    {
        return $this->belongsTo('App\Models\Finance\Finance','finance_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function getIncomeWithSeparatorAttribute()
    {
        return number_format($this->nominal_income, 0, ',', '.');
    }

    public function getOutcomeWithSeparatorAttribute()
    {
        return number_format($this->nominal_outcome, 0, ',', '.');
    }

    public function getIncomeRealWithSeparatorAttribute()
    {
        return number_format($this->nominal_income_real, 0, ',', '.');
    }

    public function getOutcomeRealWithSeparatorAttribute()
    {
        return number_format($this->nominal_outcome_real, 0, ',', '.');
    }
}
