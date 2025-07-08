<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSikap extends Model
{
    use HasFactory;
    protected $table = "report_attitude_score";
    protected $guarded = [];

    public function rasType()
    {
        return $this->belongsTo('App\Models\RasType', 'ras_type_id');
    }

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'score_id');
    }

    public function scopeSpiritual($query)
    {
        return $query->where('ras_type_id', 1);
    }

    public function scopeSosial($query)
    {
        return $query->where('ras_type_id', 2);
    }
}
