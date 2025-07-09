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
