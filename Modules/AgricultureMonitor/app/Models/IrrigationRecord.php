<?php

namespace Modules\AgricultureMonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\AgricultureMonitor\Database\Factories\IrrigationRecordFactory;

class IrrigationRecord extends Model
{
    use HasFactory;

    protected $table = "log_irrigation_records";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'planting_cycle_id',
        'irrigation_time'
    ];

    public $timestamps = false;

    // protected static function newFactory(): IrrigationRecordFactory
    // {
    //     // return IrrigationRecordFactory::new();
    // }

    public function plantingCycle()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\PlantingCycle', 'planting_cycle_id');
    }
}
