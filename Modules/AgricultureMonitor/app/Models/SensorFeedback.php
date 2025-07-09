<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\SensorFeedbackFactory;

class SensorFeedback extends Model
{
    use HasFactory;

    protected $table = "tref_sensor_feedback";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'plant_id',
        'sensor_id',
        'value',
        'min',
        'max',
        'feedback'
    ];

    // protected static function newFactory(): SensorFeedbackFactory
    // {
    //     // return SensorFeedbackFactory::new();
    // }

    public function plant()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\Plant', 'plant_id');
    }

    public function sensor()
    {
        return $this->belongsTo('Modules\AgricultureMonitor\Models\Sensor', 'sensor_id');
    }
}
