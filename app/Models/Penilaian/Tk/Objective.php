<<<<<<< HEAD
<?php

namespace App\Models\Penilaian\Tk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasFactory;
    protected $table = "rkd_objectives";
    protected $fillable = [
        'level_id',
		'employee_id',
		'desc'
    ];

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }
    
    public function elements()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\ObjectiveElement', 'objective_id');
    }
    
    public function predicates()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\FormatifKualitatif', 'objective_id');
    }
}
=======
<?php

namespace App\Models\Penilaian\Tk;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objective extends Model
{
    use HasFactory;
    protected $table = "rkd_objectives";
    protected $fillable = [
        'level_id',
		'employee_id',
		'desc'
    ];

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }
    
    public function elements()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\ObjectiveElement', 'objective_id');
    }
    
    public function predicates()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\FormatifKualitatif', 'objective_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
