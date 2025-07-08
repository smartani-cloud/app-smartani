<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceType extends Model
{
    use HasFactory;

    protected $table = "tref_finance_types";
    protected $fillable = ['name'];

    public function finances()
    {
        return $this->hasMany('App\Models\Finance\FinanceDetail','type_id');
    }
}
