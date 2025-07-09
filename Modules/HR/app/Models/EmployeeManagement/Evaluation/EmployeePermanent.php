<<<<<<< HEAD
<?php

namespace Modules\HR\Models\EmployeeManagement\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class EmployeePermanent extends Model
{
    use HasFactory;

    protected $table = "trx_employee_permanents";

    protected $fillable = [
        'employee_id',
        'promotion_date'
    ];

    public function employee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_id');
    }

    public function getPromotionDateIdAttribute()
    {
        if (!$this->promotion_date) {
            return null; // Menghindari error jika tidak ada nilai
        }

        Date::setLocale('id');
        return Date::parse($this->promotion_date)->format('j F Y');
    }
}
=======
<?php

namespace Modules\HR\Models\EmployeeManagement\Evaluation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class EmployeePermanent extends Model
{
    use HasFactory;

    protected $table = "trx_employee_permanents";

    protected $fillable = [
        'employee_id',
        'promotion_date'
    ];

    public function employee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_id');
    }

    public function getPromotionDateIdAttribute()
    {
        if (!$this->promotion_date) {
            return null; // Menghindari error jika tidak ada nilai
        }

        Date::setLocale('id');
        return Date::parse($this->promotion_date)->format('j F Y');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
