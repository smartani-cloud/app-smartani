<<<<<<< HEAD
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPenerimaan extends Model
{
    use HasFactory;

    protected $table = "tref_acceptance_status";

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','acceptance_status_id');
    }
}
=======
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusPenerimaan extends Model
{
    use HasFactory;

    protected $table = "tref_acceptance_status";

    public function calonPegawai()
    {
        return $this->hasMany('App\Models\Rekrutmen\CalonPegawai','acceptance_status_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
