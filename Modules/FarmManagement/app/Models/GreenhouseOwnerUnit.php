<<<<<<< HEAD
<?php

namespace Modules\HR\App\Models\FarmManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GreenhouseOwnerUnit extends Pivot
{
    use HasFactory;

    protected $table = "tas_greenhouse_owner_units";

    public $incrementing = true;

    public function owner()
    {
        return $this->belongsTo('Modules\HR\Models\FarmManagement\GreenhouseOwner','owner_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }
}
=======
<?php

namespace Modules\HR\App\Models\FarmManagement;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class GreenhouseOwnerUnit extends Pivot
{
    use HasFactory;

    protected $table = "tas_greenhouse_owner_units";

    public $incrementing = true;

    public function owner()
    {
        return $this->belongsTo('Modules\HR\Models\FarmManagement\GreenhouseOwner','owner_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
