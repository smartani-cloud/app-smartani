<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\IrrigationSystemFactory;

class IrrigationSystem extends Model
{
    use HasFactory;

    protected $table = "tref_irrigation_systems";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'code',
        'name',
        'desc'
    ];

    public $timestamps = false;

    // protected static function newFactory(): IrrigationSystemFactory
    // {
    //     // return IrrigationSystemFactory::new();
    // }

    public function greenhouses()
    {
        return $this->hasMany('Modules\FarmManagement\Models\Greenhouse', 'irrigation_system_id');
    }
}
