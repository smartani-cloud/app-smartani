<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapelKelas extends Model
{
    use HasFactory;
    protected $table = "subject_class";
    protected $fillable = [
        'subject_id', 
        'level_id', 
    ];

    public function mapel()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran','subject_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Level','level_id');
    }
}
