<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsDeductionYear extends Model
{
    use HasFactory;
    protected $table = "tm_bms_deduction_year";
    protected $fillable = [
        'unit_id',
        'academic_year_id',
        'nominal',
    ];
}
