<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SppYearTotal extends Model
{
    use HasFactory;
    protected $table = "tm_spp_year_total";
    protected $fillable = [
        'unit_id',
        'year',
        'total_nominal',
    ];

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa', 'student_id');
    }
}
