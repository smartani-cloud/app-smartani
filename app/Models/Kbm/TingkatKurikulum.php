<?php

namespace App\Models\Kbm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TingkatKurikulum extends Model
{
    use HasFactory;

    protected $table = "tas_level_curriculum";
    protected $fillable = [
        'semester_id',
        'level_id',
		'curriculum_id',
	];
	
	public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester', 'semester_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo('App\Models\Kbm\Kurikulum','curriculum_id');
    }
}
