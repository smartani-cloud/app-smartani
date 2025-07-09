<<<<<<< HEAD
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatarBidangStudi extends Model
{
    use HasFactory;

    protected $table = "tref_academic_background";

    protected $fillable = ['name'];

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','academic_background_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','academic_background_id');
    }
}
=======
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LatarBidangStudi extends Model
{
    use HasFactory;

    protected $table = "tref_academic_background";

    protected $fillable = ['name'];

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','academic_background_id');
    }

    public function pegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\Pegawai','academic_background_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
