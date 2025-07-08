<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiIklas extends Model
{
    use HasFactory;

    protected $table = "rkd_iklas_scores";
    protected $fillable = [
        'report_score_id',
        'competence_id',
		'predicate'
    ];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }
	
    public function kompetensi()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\KompetensiIklas', 'competence_id');
    }
}
