<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\PlantFactory;

class Plant extends Model
{
    protected $table = "tm_plants";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'type_id',
        'name',
        'scientific_name',
        'growth_cycle_days',
        'yield_per_hole_min',
        'yield_per_hole_max',
        'fruit_weight_min_g',
        'fruit_weight_max_g',
        'daily_watering_min',
        'daily_watering_max'
    ];

    // protected static function newFactory(): PlantFactory
    // {
    //     // return PlantFactory::new();
    // }

    public function type()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantType', 'type_id');
    }

    public function plantingCycles()
    {
        return $this->hasMany('Modules\FarmManagement\Models\PlantingCycle', 'plant_id');
    }

    public function growthPredictions()
    {
        return $this->hasMany('Modules\AgricultureMonitor\Models\PlantGrowthPrediction', 'plant_id');
    }
}
