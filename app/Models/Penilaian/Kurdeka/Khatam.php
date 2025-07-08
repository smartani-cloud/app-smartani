<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Khatam extends Model
{
    use HasFactory;
    protected $table = "rkd_khatam";
    protected $fillable = [
        'report_score_id',
        'type_id',
		'last',
		'total',
        'percentage',
    ];
	
    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }
	
    public function type()
    {
        return $this->belongsTo('App\Models\Penilaian\Kurdeka\KhatamType', 'type_id');
    }
}
