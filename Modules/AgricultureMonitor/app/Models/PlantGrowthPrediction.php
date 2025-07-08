<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\PlantGrowthPredictionFactory;

class PlantGrowthPrediction extends Model
{
    use HasFactory;

    protected $table = "tref_plant_growth_predictions";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'plant_id',
        'day_number',
        'expected_height_cm'
    ];

    // protected static function newFactory(): PlantGrowthPredictionFactory
    // {
    //     // return PlantGrowthPredictionFactory::new();
    // }

    public function plant()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\Plant', 'plant_id');
    }
}
