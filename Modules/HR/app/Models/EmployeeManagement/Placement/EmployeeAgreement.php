<<<<<<< HEAD
<?php

namespace Modules\HR\App\Models\EmployeeManagement\Placement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class EmployeeAgreement extends Model
{
    use HasFactory;

    protected $table = "trx_employee_agreements";

    protected $fillable = [
        'employee_id',
        'reference_number',
        'party_1_name',
        'party_1_position',
        'party_1_address',
        'employee_name',
        'employee_address',
        'employee_status',
        'period_start',
        'period_end',
        'status_id'
    ];

    public function employee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_id');
    }

    public function activeStatus()
    {
        return $this->belongsTo('Modules\Core\Models\References\Status','status_id');
    }

    public function getPeriodIdAttribute()
    {
        if (!$this->period_start || !$this->period_end) {
            return null;
        }

        Date::setLocale('id');
        return Date::parse($this->period_start)->format('j F Y').' s.d. '.Date::parse($this->period_end)->format('j F Y');
    }

    public function getRemainingPeriodAttribute()
    {
        if (!$this->period_end) {
            return null;
        }

        $period_end = Date::parse($this->period_end);
        $now = Date::parse(Date::now('Asia/Jakarta')->format('Y-m-d'));
        $date = $period_end->diffInDays($now);

        return $period_end->lessThan($now) ? 'Habis' : $date.' hari';
    }

    public function getEmployeeStatusAcronymAttribute()
    {
        return !$this->employee_status ? strtoupper(implode('', array_map(fn($word) => $word[0], explode(' ', $this->employee_status)))) : null;
    }
    
    public function scopeActive($query){
        return $query->where('status_id',5);
    }

    public function scopeInactive($query){
        return $query->where('status_id',6);
    }
}
=======
<?php

namespace Modules\HR\App\Models\EmployeeManagement\Placement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class EmployeeAgreement extends Model
{
    use HasFactory;

    protected $table = "trx_employee_agreements";

    protected $fillable = [
        'employee_id',
        'reference_number',
        'party_1_name',
        'party_1_position',
        'party_1_address',
        'employee_name',
        'employee_address',
        'employee_status',
        'period_start',
        'period_end',
        'status_id'
    ];

    public function employee()
    {
        return $this->belongsTo('Modules\HR\Models\EmployeeManagement\Recruitment\Employee','employee_id');
    }

    public function activeStatus()
    {
        return $this->belongsTo('Modules\Core\Models\References\Status','status_id');
    }

    public function getPeriodIdAttribute()
    {
        if (!$this->period_start || !$this->period_end) {
            return null;
        }

        Date::setLocale('id');
        return Date::parse($this->period_start)->format('j F Y').' s.d. '.Date::parse($this->period_end)->format('j F Y');
    }

    public function getRemainingPeriodAttribute()
    {
        if (!$this->period_end) {
            return null;
        }

        $period_end = Date::parse($this->period_end);
        $now = Date::parse(Date::now('Asia/Jakarta')->format('Y-m-d'));
        $date = $period_end->diffInDays($now);

        return $period_end->lessThan($now) ? 'Habis' : $date.' hari';
    }

    public function getEmployeeStatusAcronymAttribute()
    {
        return !$this->employee_status ? strtoupper(implode('', array_map(fn($word) => $word[0], explode(' ', $this->employee_status)))) : null;
    }
    
    public function scopeActive($query){
        return $query->where('status_id',5);
    }

    public function scopeInactive($query){
        return $query->where('status_id',6);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
