<<<<<<< HEAD
<?php

namespace App\Models\Rkat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rkat extends Model
{
    use HasFactory;

    protected $table = "rkat";
    protected $fillable = [
    	'year',
        'academic_year_id',
    	'budgeting_budgeting_type_id',
    	'employee_id',
    	'finance_acc_id',
    	'finance_acc_status_id',
    	'finance_acc_time',
    	'director_acc_id',
    	'director_acc_status_id',
    	'director_acc_time',
        'revision',
        'is_active',
        'is_final',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function jenisAnggaranAnggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_budgeting_type_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function accKeuangan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','finance_acc_id');
    }

    public function accKeuanganStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','finance_acc_status_id');
    }

    public function accDirektur()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','director_acc_id');
    }

    public function accDirekturStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','director_acc_status_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Rkat\RkatDetail','rkat_id');
    }
    
    public function scopeAktif($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeFinal($query)
    {
        return $query->where('is_final',1);
    }
    
    public function scopeUnfinal($query)
    {
        return $query->where('is_final',0);
    }
}
=======
<?php

namespace App\Models\Rkat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rkat extends Model
{
    use HasFactory;

    protected $table = "rkat";
    protected $fillable = [
    	'year',
        'academic_year_id',
    	'budgeting_budgeting_type_id',
    	'employee_id',
    	'finance_acc_id',
    	'finance_acc_status_id',
    	'finance_acc_time',
    	'director_acc_id',
    	'director_acc_status_id',
    	'director_acc_time',
        'revision',
        'is_active',
        'is_final',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo('App\Models\Kbm\TahunAjaran','academic_year_id');
    }

    public function jenisAnggaranAnggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_budgeting_type_id');
    }

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function accKeuangan()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','finance_acc_id');
    }

    public function accKeuanganStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','finance_acc_status_id');
    }

    public function accDirektur()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','director_acc_id');
    }

    public function accDirekturStatus()
    {
        return $this->belongsTo('App\Models\StatusAcc','director_acc_status_id');
    }

    public function detail()
    {
        return $this->hasMany('App\Models\Rkat\RkatDetail','rkat_id');
    }
    
    public function scopeAktif($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeFinal($query)
    {
        return $query->where('is_final',1);
    }
    
    public function scopeUnfinal($query)
    {
        return $query->where('is_final',0);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
