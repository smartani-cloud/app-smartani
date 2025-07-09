<?php

namespace Modules\HR\App\Models\EmployeeManagement\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeUnitPosition extends Pivot
{
    use HasFactory;

    protected $table = "tas_employee_unit_positions";

    public $incrementing = true;

    public function employeeUnit()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\EmployeeUnit','employee_unit_id');
    }

    public function position()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Position','position_id');
    }
}
