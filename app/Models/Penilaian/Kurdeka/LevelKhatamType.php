<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelKhatamType extends Model
{
    use HasFactory;
    protected $table = "tas_level_khatam_type";
    protected $fillable = [
        'semester_id',
        'level_id',
		'khatam_type_id',
    ];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }
	
    public function type()
    {
        return $this->belongsTo('App\Models\Penilaian\Kurdeka\KhatamType', 'khatam_type_id');
    }
}
