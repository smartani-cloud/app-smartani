<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\IrrigationGuidelineFactory;

class IrrigationGuideline extends Model
{
    protected $table = "tref_irrigation_guidelines";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'plant_id',
        'temperature_low',
        'temperature_high',
        'humidity_low',
        'humidity_high',
        'lux_low',
        'lux_high',
        'irrigation_frequency'
    ];

    // protected static function newFactory(): IrrigationGuidelineFactory
    // {
    //     // return IrrigationGuidelineFactory::new();
    // }

    public function plant()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\Plant', 'plant_id');
    }
}
