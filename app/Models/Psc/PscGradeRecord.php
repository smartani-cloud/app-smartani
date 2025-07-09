<<<<<<< HEAD
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGradeRecord extends Model
{
    use HasFactory;

    protected $table = "tref_psc_grade_records";
    protected $fillable = ['grades'];
	
    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','psc_grade_record_id');
    }
}
=======
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscGradeRecord extends Model
{
    use HasFactory;

    protected $table = "tref_psc_grade_records";
    protected $fillable = ['grades'];
	
    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','psc_grade_record_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
