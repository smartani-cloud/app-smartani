<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\HarvestSummaryFactory;

class HarvestSummary extends Model
{
    use HasFactory;
    
    protected $table = "trx_harvest_summaries";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'planting_cycle_id',
        'date',
        'harvest_quality_id',
        'weight_kg',
        'harvest_category_id'
    ];

    // protected static function newFactory(): HarvestSummaryFactory
    // {
    //     // return HarvestSummaryFactory::new();
    // }

    public function plantingCycle()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantingCycle', 'planting_cycle_id');
    }

    public function harvestQuality()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\HarvestQuality', 'harvest_quality_id');
    }

    public function harvestCategory()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\HarvestCategory', 'harvest_category_id');
    }
}
