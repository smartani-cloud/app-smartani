<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeType extends Model
{
    use HasFactory;

    protected $table = "tref_outcome_types";
    protected $fillable = ['name','is_generated'];

    public function outcomes()
    {
        return $this->hasMany('App\Models\Finance\Outcome','type_id');
    }
}
