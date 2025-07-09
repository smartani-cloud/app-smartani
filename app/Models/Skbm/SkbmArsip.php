<?php

namespace App\Models\Skbm;;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkbmArsip extends Model
{
    use HasFactory;

    protected $table = "skbm_archive";

    public function skbm()
    {
        return $this->belongsTo('App\Models\Skbm\Skbm','skbm_id');
    }
}
