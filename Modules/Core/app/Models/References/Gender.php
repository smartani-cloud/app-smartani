<<<<<<< HEAD
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Gender extends Model
{
    use HasFactory;

    protected $table = "tref_gender";

    public $timestamps = false;

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','gender_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','gender_id');
    }

    public function greenhouseOwners()
    {
        return $this->hasMany('Modules\FarmManagement\Models\GreenhouseOwner','gender_id');
    }

    public function students()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Academic\Student','gender_id') : null;
    }
}
=======
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Gender extends Model
{
    use HasFactory;

    protected $table = "tref_gender";

    public $timestamps = false;

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','gender_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','gender_id');
    }

    public function greenhouseOwners()
    {
        return $this->hasMany('Modules\FarmManagement\Models\GreenhouseOwner','gender_id');
    }

    public function students()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Academic\Student','gender_id') : null;
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
