<<<<<<< HEAD
<?php

namespace Modules\HR\App\Models\EmployeeManagement\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class EmployeeStatus extends Model
{
    use HasFactory;

    protected $table = "tref_employee_statuses";

    protected $fillable = ['code', 'status', 'show_name', 'desc', 'category_id', 'status_id'];

    public function employeeCategory()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\EmployeeCategory','category_id');
    }

    public function activeStatus()
    {
        return $this->belongsTo('Modules\Core\Models\References\Status','status_id');
    }

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','employee_status_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_status_id');
    }

    public function employeEvaluations()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Evaluation\EmployeeEvaluation','recommended_employee_status_id');
    }

    public function sppDeductions()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Finance\Payment\SppDeduction','employee_status_id') : null;
    }

    public function getAcronymAttribute()
    {
        return strtoupper(implode('', array_map(fn($word) => $word[0], explode(' ', $this->status))));
    }

    public function scopeActive($query)
    {
        return $query->where('status_id', 5);
    }

    public function scopeNonPermanentEmployee($query)
    {
        return $query->where('code', 'like', '02.%');
    }

    public function scopePartner($query)
    {
        return $query->whereRelation('employeeCategory', 'name', 'Mitra');
    }

    public function scopeCandidateEmployee($query)
    {
        return $query->whereIn('code', ['02.%', '03.%']);
    }
}
=======
<?php

namespace Modules\HR\App\Models\EmployeeManagement\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class EmployeeStatus extends Model
{
    use HasFactory;

    protected $table = "tref_employee_statuses";

    protected $fillable = ['code', 'status', 'show_name', 'desc', 'category_id', 'status_id'];

    public function employeeCategory()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\EmployeeCategory','category_id');
    }

    public function activeStatus()
    {
        return $this->belongsTo('Modules\Core\Models\References\Status','status_id');
    }

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','employee_status_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_status_id');
    }

    public function employeEvaluations()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Evaluation\EmployeeEvaluation','recommended_employee_status_id');
    }

    public function sppDeductions()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Finance\Payment\SppDeduction','employee_status_id') : null;
    }

    public function getAcronymAttribute()
    {
        return strtoupper(implode('', array_map(fn($word) => $word[0], explode(' ', $this->status))));
    }

    public function scopeActive($query)
    {
        return $query->where('status_id', 5);
    }

    public function scopeNonPermanentEmployee($query)
    {
        return $query->where('code', 'like', '02.%');
    }

    public function scopePartner($query)
    {
        return $query->whereRelation('employeeCategory', 'name', 'Mitra');
    }

    public function scopeCandidateEmployee($query)
    {
        return $query->whereIn('code', ['02.%', '03.%']);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
