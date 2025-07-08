<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\PlantTypeFactory;

class PlantType extends Model
{
    use HasFactory;

    protected $table = "tref_plant_types";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'category_id',
        'name'
    ];

    // protected static function newFactory(): PlantTypeFactory
    // {
    //     // return PlantTypeFactory::new();
    // }

    public function category()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantCategory', 'category_id');
    }

    public function plants()
    {
        return $this->hasMany('Modules\FarmManagement\Models\Plant', 'type_id');
    }
}
