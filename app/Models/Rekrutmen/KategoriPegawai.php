<<<<<<< HEAD
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPegawai extends Model
{
    use HasFactory;

    protected $table = "tref_employee_category";

    public function statuses()
    {
        return $this->hasMany('App\Models\Rekrutmen\StatusPegawai','category_id');
    }
}
=======
<?php

namespace App\Models\Rekrutmen;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriPegawai extends Model
{
    use HasFactory;

    protected $table = "tref_employee_category";

    public function statuses()
    {
        return $this->hasMany('App\Models\Rekrutmen\StatusPegawai','category_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
