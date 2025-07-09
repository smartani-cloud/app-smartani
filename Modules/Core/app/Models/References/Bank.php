<<<<<<< HEAD
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Bank extends Model
{
    use HasFactory;
    
    protected $table = "tref_banks";

    protected $fillable = [
        'code',
        'name',
        'short_name'
    ];

    public $timestamps = false;

    public function studentApplicants()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Admission\StudentApplicant','bank_id') : null;
    }
}
=======
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Bank extends Model
{
    use HasFactory;
    
    protected $table = "tref_banks";

    protected $fillable = [
        'code',
        'name',
        'short_name'
    ];

    public $timestamps = false;

    public function studentApplicants()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Admission\StudentApplicant','bank_id') : null;
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
