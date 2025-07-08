<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeStatus extends Model
{
    use HasFactory;

    protected $table = "tref_outcome_statuses";
    protected $fillable = ['name'];

    public function outcomes()
    {
        return $this->hasMany('App\Models\Finance\Outcome','status_id');
    }
}
