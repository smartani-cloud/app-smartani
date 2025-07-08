<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsYearTotal extends Model
{
    use HasFactory;
    protected $table = "tm_bms_year_total";
    protected $fillable = [
        'unit_id',
        'academic_year_id',
        'nominal',
    ];
}
