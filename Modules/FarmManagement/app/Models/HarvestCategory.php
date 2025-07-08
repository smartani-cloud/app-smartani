<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\HarvestCategoryFactory;

class HarvestCategory extends Model
{
    use HasFactory;

    protected $table = "tref_harvest_categories";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name'
    ];

    public $timestamps = false;

    // protected static function newFactory(): HarvestCategoryFactory
    // {
    //     // return HarvestCategoryFactory::new();
    // }

    public function harvestSummary()
    {
        return $this->hasMany('Modules\FarmManagement\Models\HarvestSummary', 'harvest_category_id');
    }
}
