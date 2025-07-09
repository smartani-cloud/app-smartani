<<<<<<< HEAD
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscScoreIndicator extends Model
{
    use HasFactory;

    protected $table = "psc_score_indicator";
    protected $fillable = [
    	'psc_score_id',
        'code',
    	'indicator_id',
    	'score',
    	'percentage',
    	'total_score'
    ];

    public function nilai()
    {
        return $this->belongsTo('App\Models\Psc\PscScore','psc_score_id');
    }

    public function indikator()
    {
        return $this->belongsTo('App\Models\Psc\PscIndicator','indicator_id');
    }

    public function penilai()
    {
        return $this->hasMany('App\Models\Psc\PscScoreIndicatorGrader','psi_id');
    }
}
=======
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscScoreIndicator extends Model
{
    use HasFactory;

    protected $table = "psc_score_indicator";
    protected $fillable = [
    	'psc_score_id',
        'code',
    	'indicator_id',
    	'score',
    	'percentage',
    	'total_score'
    ];

    public function nilai()
    {
        return $this->belongsTo('App\Models\Psc\PscScore','psc_score_id');
    }

    public function indikator()
    {
        return $this->belongsTo('App\Models\Psc\PscIndicator','indicator_id');
    }

    public function penilai()
    {
        return $this->hasMany('App\Models\Psc\PscScoreIndicatorGrader','psi_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
