<?php

namespace Modules\HR\App\Models\EmployeeManagement\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicBackground extends Model
{
    use HasFactory;

    protected $table = "tref_academic_backgrounds";

    protected $fillable = ['name'];
    
    public $timestamps = false;

    public function candidateEmployees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\CandidateEmployee','academic_background_id');
    }

    public function employees()
    {
        return $this->hasMany('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','academic_background_id');
    }
}
