<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\SensorReadingFactory;

class SensorReading extends Model
{
    use HasFactory;

    protected $table = "log_sensor_readings";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'planting_cycle_id',
        'sensor_id',
        'value',
        'recorded_at'
    ];
    
    public $timestamps = false;

    // protected static function newFactory(): SensorReadingFactory
    // {
    //     // return SensorReadingFactory::new();
    // }    

    public function plantingCycle()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantingCycle', 'planting_cycle_id');
    }

    public function sensor()
    {
        return $this->belongsTo('Modules\AgricultureMonitor\Models\Sensor', 'sensor_id');
    }
}
