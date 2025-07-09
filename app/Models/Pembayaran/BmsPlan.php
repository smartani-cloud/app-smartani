<?php

namespace App\Models\Pembayaran;

use App\Models\Kbm\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BmsPlan extends Model
{
    use HasFactory;
    protected $table = "tm_bms_plan";
    protected $fillable = [
        'unit_id',
        'academic_year_id',
        'total_plan',
        'total_get',
        'total_student',
        'student_remain',
        'remain',
        'percent',
    ];

    public function academicYear()
    {
        return $this->belongsTo(TahunAjaran::class,'academic_year_id');
    }
}
