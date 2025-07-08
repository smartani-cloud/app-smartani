<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiHafalan extends Model
{
    use HasFactory;
    protected $table = "score_memorize";
    protected $guarded = [];

    public function nilai()
    {
        return $this->belongsTo('App\Models\Penilaian\Hafalan', 'report_memorize_id');
    }

    public function jenis()
    {
        return $this->belongsTo('App\Models\Penilaian\HafalanType', 'mem_type_id');
    }
}
