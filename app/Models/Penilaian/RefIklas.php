<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefIklas extends Model
{
    use HasFactory;

    protected $table = "tref_iklas";
    protected $guarded = [];

    public function nilaiIklas()
    {
        return $this->hasMany('App\Models\Penilaian\ScoreIklas', 'iklas_ref_id');
    }

    public function indikator()
    {
        return $this->hasMany('App\Models\Penilaian\IndikatorIklasDetail', 'iklas_ref_id');
    }

    public function getCategoryNumberAttribute()
    {
        return $this->iklas_cat.'.'.$this->iklas_no.'.';
    }

    public function getCategoryNumberCategoryAttribute()
    {
        return $this->categoryNumber.' '.$this->category;
    }
}
