<<<<<<< HEAD
<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiIklas extends Model
{
    use HasFactory;

    protected $table = "rkd_iklas_scores";
    protected $fillable = [
        'report_score_id',
        'competence_id',
		'predicate'
    ];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }
	
    public function kompetensi()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\KompetensiIklas', 'competence_id');
    }
}
=======
<?php

namespace App\Models\Penilaian\Iklas;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiIklas extends Model
{
    use HasFactory;

    protected $table = "rkd_iklas_scores";
    protected $fillable = [
        'report_score_id',
        'competence_id',
		'predicate'
    ];

    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }
	
    public function kompetensi()
    {
        return $this->belongsTo('App\Models\Penilaian\Iklas\KompetensiIklas', 'competence_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
