<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EducationLevel extends Model
{
    use HasFactory;

    protected $table = "tref_education_levels";

    protected $fillable = ['code','name','desc'];
    
    public $timestamps = false;

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','education_level_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','education_level_id');
    }
}
