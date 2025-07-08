<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiKeterampilanDetail extends Model
{
    use HasFactory;
    protected $table = "score_skill_individual";
    protected $guarded = [];
}
