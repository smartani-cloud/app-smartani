<?php

namespace App\Models\Psb;

use App\Models\Kbm\TahunAjaran;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterNumber extends Model
{
    use HasFactory;
    protected $table = "tref_psb_reg_number";
    protected $fillable = [
        'unit_id',
        'academic_year_id',
        'number',
    ];

    public function year()
    {
        return $this->belongsTo(TahunAjaran::class,'academic_year_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
}
