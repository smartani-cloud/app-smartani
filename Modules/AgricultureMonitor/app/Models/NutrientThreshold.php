<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\NutrientThresholdFactory;

class NutrientThreshold extends Model
{
    use HasFactory;

    protected $table = "tref_nutrient_thresholds";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'plant_id',
        'unit_id',
        'tds_low',
        'tds_high'
    ];

    // protected static function newFactory(): NutrientThresholdFactory
    // {
    //     // return NutrientThresholdFactory::new();
    // }

    public function plant()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\Plant', 'plant_id');
    }   

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }
}
