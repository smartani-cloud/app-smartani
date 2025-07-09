<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KDType extends Model
{
    use HasFactory;

    protected $table = "tref_kd_type";
    protected $guarded = [];
}
