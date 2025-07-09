<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RasType extends Model
{
    use HasFactory;

    protected $table = "tref_ras_type";

    public function getTypeAttribute()
    {
        return explode(' ',$this->ras_type)[1];
    }
}
