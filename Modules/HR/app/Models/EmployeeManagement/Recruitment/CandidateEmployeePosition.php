<<<<<<< HEAD
<?php

namespace Modules\HR\App\Models\EmployeeManagement\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CandidateEmployeePosition extends Pivot
{
    use HasFactory;

    protected $table = "tas_candidate_employee_positions";

    public $incrementing = true;

    public function candidateEmployee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','candidate_employee_id');
    }

    public function position()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Position','position_id');
    }
}
=======
<?php

namespace Modules\HR\App\Models\EmployeeManagement\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CandidateEmployeePosition extends Pivot
{
    use HasFactory;

    protected $table = "tas_candidate_employee_positions";

    public $incrementing = true;

    public function candidateEmployee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','candidate_employee_id');
    }

    public function position()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Position','position_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
