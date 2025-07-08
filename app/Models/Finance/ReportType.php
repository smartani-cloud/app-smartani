<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportType extends Model
{
    use HasFactory;

    protected $table = "tref_report_types";
    protected $fillable = ['name'];

    public function finances()
    {
        return $this->hasMany('App\Models\Finance\FinanceDetail','report_type_id');
    }
}
