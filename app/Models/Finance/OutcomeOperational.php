<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeOperational extends Model
{
    use HasFactory;

    protected $table = "outcome_operationals";
    protected $fillable = [
    	'outcome_id',
    	'operational_id',
    	'operational_desc',
        'amount',
        'percentage'
    ];

    public function outcome()
    {
        return $this->belongsTo('App\Models\Finance\Outcome','outcome_id');
    }

    public function operational()
    {
        return $this->belongsTo('App\Models\Project\Operational','operational_id');
    }

    public function getAmountWithSeparatorAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }
}
