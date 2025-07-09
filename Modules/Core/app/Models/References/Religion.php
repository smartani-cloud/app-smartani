<<<<<<< HEAD
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Religion extends Model
{
    use HasFactory;
    
    protected $table = "tref_religions";

    public $timestamps = false;

    public function students()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Academic\Student','religion_id') : null;
    }
}
=======
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Module;

class Religion extends Model
{
    use HasFactory;
    
    protected $table = "tref_religions";

    public $timestamps = false;

    public function students()
    {
        return Module::has('School') && Module::isEnabled('School') ? $this->hasMany('Modules\School\Models\Academic\Student','religion_id') : null;
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
