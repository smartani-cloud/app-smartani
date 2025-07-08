<?php

namespace Modules\HR\App\Models\EmployeeManagement\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use File;
use Jenssegers\Date\Date;

class CandidateEmployee extends Model
{
    use HasFactory;

    protected $table = "tm_candidate_employees";

    protected $fillable = [
        'name',
        'nickname',
        'photo',
        'nik',
        'npwp',
        'gender_id',
        'birth_place',
        'birth_date',
        'marital_status_id',
        'address',
        'rt',
        'rw',
        'region_id',
        'phone_number',
        'email',
        'education_level_id',
        'academic_background_id',
        'university_id',
        'competency_test',
        'psychological_test',
        'acceptance_status_id',
        'position_id',
        'unit_id',
        'employee_status_id',
        'period_start',
        'period_end',
        'hr_acc_id',
        'hr_acc_status_id',
        'hr_acc_time',
    ];

    public function units()
    {
        return $this->belongsToMany('Modules\Core\Models\Unit','tas_candidate_employee_units','candidate_employee_id','unit_id')->withTimestamps();
    }

    public function positions()
    {
        return $this->belongsToMany('Modules\HR\Models\EmployeeManagement\References\Position','tas_candidate_employee_positions','candidate_employee_id','position_id')->withTimestamps();
    }

    public function gender()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Gender','gender_id');
    }

    public function maritalStatus()
    {
        return $this->belongsTo('Modules\Core\Models\References\Status','marital_status_id');
    }

    public function region()
    {
        return $this->belongsTo('App\Models\Wilayah','region_id');
    }

    public function educationLevel()
    {
        return $this->belongsTo('Modules\Core\Models\References\EducationLevel','education_level_id');
    }

    public function academicBackground()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\AcademicBackground','academic_background_id');
    }

    public function university()
    {
        return $this->belongsTo('Modules\Core\Models\References\University','university_id');
    }

    public function acceptanceStatus()
    {
        return $this->belongsTo('Modules\Core\Models\References\Status','acceptance_status_id');
    }

    public function recommendedPosition()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Position','position_id');
    }

    public function recommendedUnit()
    {
        return $this->belongsTo('Modules\Core\Models\Unit','unit_id');
    }

    public function employeeStatus()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\EmployeeStatus','employee_status_id');
    }

    public function hrAcc()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','hr_acc_id');
    }

    public function accStatus()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Status','hr_acc_status_id');
    }

    public function getBirthDateIdAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        Date::setLocale('id');
        return Date::parse($this->birth_date)->format('j F Y');
    }

    public function getAgeAttribute()
    {
        if (!$this->birth_date) {
            return null;
        }

        $birth_date = Date::parse($this->birth_date);
        $age = Date::now()->diffInYears($birth_date);
                return $age.' tahun';
    }

    public function getPhotoPathAttribute()
    {
        return $this->photo ? 'img/photo/candidate/'.$this->photo : null;
    }

    public function getShowPhotoAttribute(){
        return File::exists($this->photoPath) ? $this->photoPath : 'img/avatar/default.png';
    }

    public function getRegionCodeAttribute()
    {
        return $this->region->code;
    }

    public function getPeriodIdAttribute()
    {   
        if (!$this->period_start || !$this->period_end) {
            return null;
        }

        Date::setLocale('id');
        return Date::parse($this->period_start)->format('j F Y').' s.d. '.Date::parse($this->period_end)->format('j F Y');
    }
}
