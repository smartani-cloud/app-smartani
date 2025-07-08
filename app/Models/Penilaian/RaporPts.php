<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaporPts extends Model
{
    use HasFactory;

    protected $table = "report_mid_score";
    protected $guarded = [];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }
}
