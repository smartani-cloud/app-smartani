<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhatamType extends Model
{
    use HasFactory;
    protected $table = "tref_khatam_types";
    protected $fillable = [
        'name',
    ];

    public function levels()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\LevelKhatamType', 'khatam_type_id');
    }

    public function khatam()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\Khatam', 'khatam_type_id');
    }
}
