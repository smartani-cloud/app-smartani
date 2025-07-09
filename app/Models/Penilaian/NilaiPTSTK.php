<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiPTSTK extends Model
{
    use HasFactory;

    protected $table = "report_mid_kg_score";
    protected $guarded = [];

    public function deskripsi()
    {
        return $this->belongsTo('App\Models\Penilaian\DeskripsiAspek', 'aspect_description_id');
    }
}
