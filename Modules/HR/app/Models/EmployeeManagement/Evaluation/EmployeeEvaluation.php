<?php

namespace Modules\HR\Models\EmployeeManagement\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeEvaluation extends Model
{
    use HasFactory;

    protected $table = "trx_employee_evaluations";

    protected $fillable = [
        'employee_id',
        'temp_psc_grade_id',
        'supervision_result',
        'interview_result',
        'recommend_status_id',
        'recommended_employee_status_id',
        'dismissal_reason_id',
        'hr_acc_id',
        'hr_acc_status_id',
        'hr_acc_time'
    ];

    public function employee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_id');
    }

    public function tempPscGrade()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\PscGrade','temp_psc_grade_id');
    }

    public function recommendStatus()
    {        
        return $this->belongsTo('Modules\Core\Models\References\Status','recommend_status_id');
    }

    public function recommendedEmployeeStatus()
    {        
        return $this->belongsTo('Modules\Core\Models\References\Status','recommended_employee_status_id');
    }

    public function dismissalReason()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Dismissal\DismissalReason','dismissal_reason_id');
    }

    public function hrAcc()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','hr_acc_id');
    }

    public function hrAccStatus()
    {
        return $this->belongsTo('Modules\Core\Models\References\Status','hr_acc_status_id');
    }
}
