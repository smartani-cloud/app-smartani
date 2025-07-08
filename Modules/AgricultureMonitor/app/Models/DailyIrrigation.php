<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\DailyIrrigationFactory;

class DailyIrrigation extends Model
{
    use HasFactory;
    
    protected $table = "trx_daily_irrigation";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'planting_cycle_id',
        'temperature',
        'humidity',
        'lux',
        'recorded_at',
        'irrigation_frequency'
    ];

    public $timestamps = false;

    // protected static function newFactory(): DailyIrrigationFactory
    // {
    //     // return DailyIrrigationFactory::new();
    // }

    public function plantingCycle()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantingCycle', 'planting_cycle_id');
    }
}
