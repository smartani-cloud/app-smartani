<<<<<<< HEAD
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGrade extends Model
{
    use HasFactory;

    protected $table = "tref_psc_grade";
    protected $fillable = ['set_id','name','start','end'];

    public function set()
    {
        return $this->belongsTo('App\Models\Psc\PscGradeSet','set_id');
    }

    public function evaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','temp_psc_grade_id');
    }

    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','grade_id');
    }
}
=======
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGrade extends Model
{
    use HasFactory;

    protected $table = "tref_psc_grade";
    protected $fillable = ['set_id','name','start','end'];

    public function set()
    {
        return $this->belongsTo('App\Models\Psc\PscGradeSet','set_id');
    }

    public function evaluasiPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\EvaluasiPegawai','temp_psc_grade_id');
    }

    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','grade_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
