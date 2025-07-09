<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscScoreIndicatorGrader extends Model
{
    use HasFactory;

    protected $table = "psc_score_indicator_grader";
    protected $fillable = [
    	'psi_id',
    	'score',
    	'grader_id',
    	'position_id',
    	'position_desc'
    ];

    public function indikator()
    {
        return $this->belongsTo('App\Models\Psc\PscScoreIndicator','psi_id');
    }

    public function penilai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','grader_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }
}
