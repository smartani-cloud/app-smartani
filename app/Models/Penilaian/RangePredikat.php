<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RangePredikat extends Model
{
    use HasFactory;

    protected $table = "report_score_range";
    protected $guarded = [];

    public function mapel()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran', 'subject_id');
    }
}
