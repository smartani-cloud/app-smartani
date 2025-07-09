<<<<<<< HEAD
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiUnit extends Model
{
    use HasFactory;

    protected $table = "employee_unit";

    protected $fillable = [
        'employee_id',
        'unit_id'
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function jabatans()
    {
        return $this->belongsToMany('App\Models\Penempatan\Jabatan','employee_position','employee_unit_id','position_id')->withTimestamps();
    }
}
=======
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PegawaiUnit extends Model
{
    use HasFactory;

    protected $table = "employee_unit";

    protected $fillable = [
        'employee_id',
        'unit_id'
    ];

    public function pegawai()
    {
        return $this->belongsTo('App\Models\Rekrutmen\Pegawai','employee_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function jabatans()
    {
        return $this->belongsToMany('App\Models\Penempatan\Jabatan','employee_position','employee_unit_id','position_id')->withTimestamps();
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
