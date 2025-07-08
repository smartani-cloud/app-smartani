<?php

namespace App\Models\Penilaian\Tk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormatifKualitatif extends Model
{
    use HasFactory;

    protected $table = "rkd_qualitative_formatives";
    protected $fillable = [
        'report_score_id',
        'objective_id',
		'predicate',
        'score'
    ];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }
	
    public function objective()
    {
        return $this->belongsTo('App\Models\Penilaian\Tk\Objective', 'objective_id');
    }
}
