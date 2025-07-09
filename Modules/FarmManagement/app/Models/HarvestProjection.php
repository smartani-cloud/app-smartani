<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\HarvestProjectionFactory;

class HarvestProjection extends Model
{
    use HasFactory;

    protected $table = "tref_harvest_projection";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'planting_cycle_id',
        'min_price_per_ounce',
        'max_price_per_ounce'
    ];

    // protected static function newFactory(): HarvestProjectionFactory
    // {
    //     // return HarvestProjectionFactory::new();
    // }

    public function plantingCycle()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantingCycle', 'planting_cycle_id');
    }

    public function getMinPricePerOunceWithSeparatorAttribute()
    {
        return number_format($this->min_price_per_ounce, 0, ',', '.');
    }

    public function getMaxPricePerOunceWithSeparatorAttribute()
    {
        return number_format($this->max_price_per_ounce, 0, ',', '.');
    }
}
