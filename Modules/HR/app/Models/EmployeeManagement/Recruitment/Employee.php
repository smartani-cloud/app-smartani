<?php

namespace Modules\HR\App\Models\EmployeeManagement\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

use App\Helpers\PhoneHelper;

use File;
use Jenssegers\Date\Date;

class Employee extends Model
{
    use HasFactory;

    protected $table = "tm_employees";

    protected $fillable = [
        'name',
        'nickname',
        'photo',
        'nip',
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
        'position_id',
        'unit_id',
        'employee_status_id',
        'join_date',
        'disjoin_date',
        'join_badge_status_id',
        'disjoin_badge_status_id',
        'active_status_id',
    ];

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

    public function position()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Position','position_id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Core\Models\Unit','unit_id');
    }

    public function employeeStatus()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\EmployeeStatus','employee_status_id');
    }

    public function joinBadgeActiveStatus()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Status','join_badge_status_id');
    }

    public function disjoinBadgeActiveStatus()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Status','disjoin_badge_status_id');
    }

    public function activeStatus()
    {
        
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\References\Status','active_status_id');
    }

    public function units()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\EmployeeUnit','employee_id');
    }

    public function agreements()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Placement\EmployeeAgreement','employee_id');
    }

    public function permanents()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Evaluation\EmployeePermanent','employee_id');
    }

    public function evaluations()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Evaluation\EmployeeEvaluation','employee_id');
    }

    public function placements()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Placement\PlacementEmployeeDetail','employee_id');
    }

    public function trainings()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Training\Training','speaker_id');
    }

    public function trainingAttendances()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Training\TrainingAttendance','employee_id');
    }

    public function approvedCandidates()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Recruitment\CandidateEmployee','hr_acc_id');
    }

    public function approvedPlacements()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Placement\PlacementEmployeeDetail','acc_employee_id');
    }

    public function approvedEvaluations()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Evaluation\EmployeeEvaluation','hr_acc_id');
    }

    public function approvedTrainings()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Training\Training','hr_acc_id');
    }

    public function employeeDismissal()
    {
        return $this->hasOne('Modules\HR\App\Models\EmployeeManagement\Dismissal\EmployeeDismissal','employee_id');
    }

    public function approvedDismissals()
    {
        return $this->hasMany('Modules\HR\App\Models\EmployeeManagement\Dismissal\EmployeeDismissal','director_acc_id');
    }

    // PSC

    public function pscScores()
    {
        return $this->hasMany('Modules\HR\App\Models\PSC\PscScore','employee_id');
    }

    // School

    public function classes()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Data\Class','teacher_id') : null;
    }

    public function schedules()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Academic\Schedule','teacher_id') : null;
    }

    public function predicateDescs()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Assessment\PredicateDesc','employee_id') : null;
    }

    public function user()
    {
        return $this->hasOne('Modules\Core\Models\UserProfile','user_id')->where('role_id','!=',36);
    }

    public function getTitleNameAttribute()
    {
        return ($this->gender_id == 1 ? 'Bapak ' : 'Ibu ').$this->name;
    }

    public function getBirthDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->birth_date)->format('j F Y');
    }

    public function getAgeAttribute()
    {
        return Date::parse($this->birth_date)->age . ' tahun';
    }

    public function getAgeOriginalAttribute()
    {
        return Date::parse($this->birth_date)->age;
    }

    public function getPhotoPathAttribute()
    {
        if($this->photo) return 'img/photo/employee/'.$this->photo;
        else return null;
    }

    public function getShowPhotoAttribute(){
        return File::exists($this->photoPath) ? $this->photoPath : 'img/avatar/default.png';
    }

    public function getRegionCodeAttribute()
    {
        return $this->region->code;
    }

    public function getYearsOfServiceAttribute()
    {
        $join = Date::parse($this->join_date);
        if(!$this->disjoin_date){
            $today = Date::now('Asia/Jakarta');
            $interval = $join->diffInDays($today);
            if($interval < $today->diffInDays(Date::now('Asia/Jakarta')->subMonth())){
                return $interval.' hari';
            }
            elseif($interval < $today->diffInDays(Date::now('Asia/Jakarta')->subYear())){
                $interval = $join->diffInMonths($today);
                return $interval.' bulan';
            }
            else{
                $interval = $join->diffInYears($today);
                return $interval.' tahun';
            }
        }
        else{
            $disjoin = Date::parse($this->disjoin_date);
            $interval = $join->diffInDays($disjoin);
            if($interval < $disjoin->diffInDays(Date::parse($this->disjoin_date)->subMonth())){
                return $interval.' hari';
            }
            elseif($interval < $disjoin->diffInDays(Date::parse($this->disjoin_date)->subYear())){
                $interval = $join->diffInMonths($disjoin);
                return $interval.' bulan';
            }
            else{
                $interval = $join->diffInYears($disjoin);
                return $interval.' tahun';
            }
        }
    }

    public function getPhoneNumberIdAttribute()
    {
        $phone_number = $this->phone_number[0] == '0' ? '+62'.substr($this->phone_number, 1) : $this->phone_number;
        return $phone_number;
    }
    
    public function getPhoneNumberWithDashIdAttribute()
    {
        return PhoneHelper::addDashesId((string)$this->phone_number,'mobile','0');
    }

    public function getJoinDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->join_date)->format('j F Y');
    }

    public function getRemainingPeriodAttribute(){
        if($this->agreements()->active()->count() > 0){
            $agreements = $this->agreements()->select('id','employee_id','period_end')->active()->latest()->first();
            if($agreements){
                return $agreements->remainingPeriod;
            }
            return null;
        }
        else return null;
    }
    
    public function scopeActive($query)
    {
        return $query->where('active_status_id',1);
    }

    public function scopeNonPermanent($query)
    {
        return $query->whereIn('employee_status_id',[3,4,5]);
    }
}
