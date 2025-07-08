<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeskripsiAspek extends Model
{
    use HasFactory;

    protected $table = "report_aspect_description";
    protected $guarded = [];

    public function aspek()
    {
        return $this->belongsTo('App\Models\Penilaian\AspekPerkembangan', 'development_aspect_id');
    }
}
