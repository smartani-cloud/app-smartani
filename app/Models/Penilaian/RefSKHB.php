<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefSKHB extends Model
{
    use HasFactory;

    protected $table = "tref_skhb_score";
    protected $guarded = [];
}
