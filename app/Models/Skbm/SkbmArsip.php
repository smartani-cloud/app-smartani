<<<<<<< HEAD
<?php

namespace App\Models\Skbm;;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkbmArsip extends Model
{
    use HasFactory;

    protected $table = "skbm_archive";

    public function skbm()
    {
        return $this->belongsTo('App\Models\Skbm\Skbm','skbm_id');
    }
}
=======
<?php

namespace App\Models\Skbm;;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkbmArsip extends Model
{
    use HasFactory;

    protected $table = "skbm_archive";

    public function skbm()
    {
        return $this->belongsTo('App\Models\Skbm\Skbm','skbm_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
