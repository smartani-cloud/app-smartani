<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\SensorFactory;

class Sensor extends Model
{
    use HasFactory;

    protected $table = "tref_sensors";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'unit'
    ];

    public $timestamps = false;

    // protected static function newFactory(): SensorFactory
    // {
    //     // return SensorFactory::new();
    // }

    public function datas()
    {
        return $this->hasMany('Modules\AgricultureMonitor\Models\SensorReading', 'sensor_id');
    }

    public function feedback()
    {
        return $this->hasMany('Modules\AgricultureMonitor\Models\SensorFeedback', 'sensor_id');
    }
}
