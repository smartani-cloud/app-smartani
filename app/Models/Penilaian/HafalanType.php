<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HafalanType extends Model
{
    use HasFactory;
    protected $table = "tref_memorize_type";
    protected $guarded = [];

    public function nilai()
    {
        return $this->hasMany('App\Models\Penilaian\NilaiHafalan', 'mem_type_id');
    }
}
