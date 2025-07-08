<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppPlan extends Model
{
    use HasFactory;
    protected $table = "tm_spp_plan";
    protected $fillable = [
        'unit_id',
        'month',
        'year',
        'total_plan',
        'total_get',
        'total_student',
        'student_remain',
        'remain',
        'percent',
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }
}
