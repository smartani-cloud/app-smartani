<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deskripsi extends Model
{
    use HasFactory;
    protected $table = "rkd_descs";
    protected $fillable = [
        'report_score_id',
		'rpd_type_id',
        'rpd_id'
    ];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }
	
    public function jenis()
    {
        return $this->belongsTo('App\Models\Penilaian\RpdType', 'rpd_type_id');
    }
	
    public function deskripsi()
    {
        return $this->belongsTo('App\Models\Penilaian\PredikatDeskripsi', 'rpd_id');
    }
}
