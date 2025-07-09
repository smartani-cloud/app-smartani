<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\HarvestDistributionFactory;

class HarvestDistribution extends Model
{
    use HasFactory;
    
    protected $table = "trx_harvest_distributions";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'planting_cycle_id',
        'client_id',
        'date',
        'harvest_quality_id',
        'weight_kg'
    ];

    // protected static function newFactory(): HarvestDistributionFactory
    // {
    //     // return HarvestDistributionFactory::new();
    // }    

    public function plantingCycle()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantingCycle', 'planting_cycle_id');
    }

    public function client()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\Client', 'client_id');
    }

    public function harvestQuality()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\HarvestQuality', 'harvest_quality_id');
    }
}
