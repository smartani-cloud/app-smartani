<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arsip extends Model
{
    use HasFactory;
    protected $table = "ijazah_skhb_archive";
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'unit_id',
        'file',
        'archive_type_id',
    ];

    

    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\Siswa','student_id');
    }

    public function scopeSkhb($query)
    {
        return $query->where('archive_type_id', 2);
    }

    public function scopeIjazah($query)
    {
        return $query->where('archive_type_id', 1);
    }
}
