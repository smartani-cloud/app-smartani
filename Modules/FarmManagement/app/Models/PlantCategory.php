<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\PlantCategoryFactory;

class PlantCategory extends Model
{
    use HasFactory;

    protected $table = "tref_plant_categories";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name'
    ];

    public $timestamps = false;

    // protected static function newFactory(): PlantCategoryFactory
    // {
    //     // return PlantCategoryFactory::new();
    // }

    public function plantTypes()
    {
        return $this->hasMany('Modules\FarmManagement\Models\PlantType', 'category_id');
    }
}
