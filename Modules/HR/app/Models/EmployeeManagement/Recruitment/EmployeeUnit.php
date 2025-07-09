<?php

namespace Modules\HR\App\Models\EmployeeManagement\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeUnit extends Model
{
    use HasFactory;

    protected $table = "tas_employee_units";

    protected $fillable = [
        'employee_id',
        'unit_id'
    ];

    public function positions()
    {
        return $this->belongsToMany('Modules\HR\Models\EmployeeManagement\References\Position','tas_employee_unit_positions','employee_unit_id','position_id')->withTimestamps();
    }

    public function employee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_id');
    }

    public function unit()
    {
        return $this->belongsTo('Modules\Core\Models\Unit','unit_id');
    }
}
