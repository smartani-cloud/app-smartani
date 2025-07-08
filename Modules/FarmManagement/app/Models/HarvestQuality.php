<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\HarvestQualityFactory;

class HarvestQuality extends Model
{
    use HasFactory;

    protected $table = "tref_harvest_qualities";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name'
    ];

    public $timestamps = false;

    // protected static function newFactory(): HarvestQualityFactory
    // {
    //     // return HarvestQualityFactory::new();
    // }

    public function harvestDistributions()
    {
        return $this->hasMany('Modules\FarmManagement\Models\HarvestDistribution', 'harvest_quality_id');
    }

    public function harvestSummaries()
    {
        return $this->hasMany('Modules\FarmManagement\Models\HarvestSummary', 'harvest_category_id');
    }
}
