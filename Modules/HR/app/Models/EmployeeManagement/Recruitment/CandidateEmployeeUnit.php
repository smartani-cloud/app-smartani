<?php

namespace Modules\HR\App\Models\EmployeeManagement\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CandidateEmployeeUnit extends Pivot
{
    use HasFactory;

    protected $table = "tas_candidate_employee_units";

    public $incrementing = true;

    public function candidateEmployee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','candidate_employee_id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Core\Models\Unit','unit_id');
    }
}
