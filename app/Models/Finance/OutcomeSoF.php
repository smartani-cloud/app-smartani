<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeSoF extends Model
{
    use HasFactory;

    protected $table = "outcome_sof";
    protected $fillable = [
    	'outcome_id',
    	'sof_id',
        'nominal',
        'percentage'
    ];

    public function outcome()
    {
        return $this->belongsTo('App\Models\Finance\Outcome','outcome_id');
    }

    public function sof()
    {
        return $this->belongsTo('App\Models\Project\SoF','sof_id');
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }
}
