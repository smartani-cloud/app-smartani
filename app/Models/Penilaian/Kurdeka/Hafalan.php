<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hafalan extends Model
{
    use HasFactory;
    protected $table = "rkd_memorizations";
    protected $fillable = [
        'report_score_id',
        'order',
		'mem_type_id',
        'desc'
    ];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }

    public function jenis()
    {
        return $this->belongsTo('App\Models\Penilaian\HafalanType', 'mem_type_id');
    }
}
