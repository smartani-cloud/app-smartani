<<<<<<< HEAD
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class PegawaiTetap extends Model
{
    use HasFactory;

    protected $table = "tm_permanent_employee";

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function getPromotionDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->promotion_date)->format('j F Y');
    }
}
=======
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class PegawaiTetap extends Model
{
    use HasFactory;

    protected $table = "tm_permanent_employee";

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function getPromotionDateIdAttribute()
    {
        Carbon::setLocale('id');
        return Carbon::parse($this->promotion_date)->format('j F Y');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
