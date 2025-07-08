<?php

namespace App\Models\Penilaian;

use GuzzleHttp\Psr7\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasTK extends Model
{
    use HasFactory;

    protected $table = "report_final_kg";
    protected $guarded = [];

    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiPASTK', 'report_final_kg_id');
    }

    public function nilairapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }
}
