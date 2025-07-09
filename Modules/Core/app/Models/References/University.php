<<<<<<< HEAD
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $table = "tref_universities";

    protected $fillable = ['name'];
    
    public $timestamps = false;

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','university_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','university_id');
    }
}
=======
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class University extends Model
{
    use HasFactory;

    protected $table = "tref_universities";

    protected $fillable = ['name'];
    
    public $timestamps = false;

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','university_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','university_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
