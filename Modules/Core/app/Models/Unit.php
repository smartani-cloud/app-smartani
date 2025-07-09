<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Unit extends Model
{
    use HasFactory;

    protected $table = "tm_units";

    protected $fillable = [
        'name',
        'full_name',
        'show_name',
        'address',
        'postal_code',
        'region_id',
        'phone_unit',
        'email',
        'website',
    ];

    // Edit
    public function jabatan()
    {
        return $this->belongsToMany('App\Models\Penempatan\Jabatan','tm_position_unit','unit_id', 'position_id')->withTimestamps();
    }

    public function candidateEmployees()
    {
        return $this->belongsToMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','tas_candidate_employee_units','unit_id','candidate_employee_id')->withTimestamps();
    }

    public function greenhouseOwners()
    {
        return $this->belongsToMany('Modules\FarmManagement\Models\GreenhouseOwner','tas_greenhouse_owner_units','unit_id','owner_id')->withTimestamps();
    }

    public function region()
    {
        return $this->belongsTo('Modules\Core\Models\References\Region','region_id');
    }

    public function activeCandidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','unit_id');
    }

    public function activeEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','unit_id');
    }

    public function employeeUnits()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\EmployeeUnit','unit_id');
    }    

    public function employeePlacements()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Placement\EmployeePlacement','unit_id');
    }

    public function trainings()
    {
        return $this->hasMany('Modules\HR\Models\Training\Training','organizer_id');
    }

    public function pscScores()
    {
        return $this->hasMany('Modules\HR\Models\PSC\PscScore','unit_id');
    }

    public function ikuAspectUnits()
    {
        return $this->hasMany('Modules\HR\Models\Iku\IkuAspectUnit','unit_id');
    }

    public function ikuAchievements()
    {
        return $this->hasMany('Modules\HR\Models\Iku\IkuAchievement','unit_id');
    }

    public function students()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Academic\Student','unit_id') : null;
    }

    public function grades()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Data\Grade','unit_id') : null;
    }
}

