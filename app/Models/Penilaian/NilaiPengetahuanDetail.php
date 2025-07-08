<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPengetahuanDetail extends Model
{
    use HasFactory;

    protected $table = "score_knowledge_individual";
    protected $guarded = [];

    public function nilaipengetahuan()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiPengetahuan', 'score_knowledge_id');
    }
}
