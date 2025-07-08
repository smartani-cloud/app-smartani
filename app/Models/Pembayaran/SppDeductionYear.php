<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppDeductionYear extends Model
{
    use HasFactory;
    protected $table = "tm_spp_deduction_year";
    protected $fillable = [
        'unit_id',
        'year',
        'total_deduction',
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }
}
