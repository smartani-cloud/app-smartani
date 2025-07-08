<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KkmPelajaran extends Model
{
    use HasFactory;
    
    protected $table = "subject_kkm";
    protected $fillable = [
        'subject_id',
        'semester_id',
        'kkm',
    ];

    public function mapel()
    {
        return $this->belongsTo('App\Models\Kbm\MataPelajaran','subject_id');
    }

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester','semester_id');
    }
}
