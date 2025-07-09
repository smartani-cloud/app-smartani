<?php

namespace Modules\HR\App\Models\EmployeeManagement\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeCategory extends Model
{
    use HasFactory;

    protected $table = "tref_employee_categories";

    public $timestamps = false;

    public function employeeStatuses()
    {
        return $this->hasMany('App\Models\Rekrutmen\StatusPegawai','category_id');
    }
}
