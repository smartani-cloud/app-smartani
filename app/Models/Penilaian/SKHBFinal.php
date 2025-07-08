<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SKHBFinal extends Model
{
    use HasFactory;

    protected $table = "skhb_final_score";
    protected $guarded = [];

    public function skhb()
    {
        return $this->belongsTo('App\Models\Penilaian\SKHB', 'skhb_id');
    }
}
