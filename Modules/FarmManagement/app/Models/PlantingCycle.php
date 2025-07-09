<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\PlantingCycleFactory;

class PlantingCycle extends Model
{
    use HasFactory;
    protected $table = "trx_planting_cycles";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'unit_id',
        'id_planting_cycle',
        'plant_id',
        'seeding_date',
        'transplanting_date',
        'total_seed_holes',
        'irrigation_duration_seconds',
        'capital_cost',
        'min_yield_kg',
        'max_yield_kg',
        'total_yield_kg'
    ];

    // protected static function newFactory(): PlantingCycleFactory
    // {
    //     // return PlantingCycleFactory::new();
    // }  

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }

    public function plant()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\Plant', 'plant_id');
    }

    public function harvestProjection()
    {
        return $this->hasOne('Modules\FarmManagement\Models\HarvestProjection', 'planting_cycle_id');
    }

    public function harvestSummaries()
    {
        return $this->hasMany('Modules\FarmManagement\Models\HarvestSummary', 'planting_cycle_id');
    }

    public function harvestDistributions()
    {
        return $this->hasMany('Modules\FarmManagement\Models\HarvestDistribution', 'planting_cycle_id');
    }

    public function irrigationRecords()
    {
        return $this->hasMany('Modules\AgricultureMonitor\Models\IrrigationRecord', 'planting_cycle_id');
    }

    public function dailyIrrigation()
    {
        return $this->hasMany('Modules\AgricultureMonitor\Models\DailyIrrigation', 'planting_cycle_id');
    }

    public function sensorReadings()
    {
        return $this->hasMany('Modules\AgricultureMonitor\Models\SensorReading', 'planting_cycle_id');
    }

    public function getTotalSeedHolesWithSeparatorAttribute()
    {
        return number_format($this->total_seed_holes, 0, ',', '.');
    }

    public function getCapitalCostWithSeparatorAttribute()
    {
        return number_format($this->capital_cost, 0, ',', '.');
    }
}