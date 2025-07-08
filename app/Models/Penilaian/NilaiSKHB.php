<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSKHB extends Model
{
    use HasFactory;

    protected $table = "skhb_score";
    protected $guarded = [];

    public function skhb()
    {
        return $this->belongsTo('App\Models\Penilaian\SKHB', 'skhb_id');
    }

    public function ref()
    {
        return $this->belongsTo('App\Models\Penilaian\RefSKHB', 'skhb_score_type_id');
    }
}
