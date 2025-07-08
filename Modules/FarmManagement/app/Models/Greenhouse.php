<?php

namespace Modules\FarmManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\FarmManagement\Database\Factories\GreenhouseFactory;

use Illuminate\Support\Facades\Storage;

class Greenhouse extends Model
{
    use HasFactory;

    protected $table = "tm_greenhouses";

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'unit_id',
        'id_greenhouse',
        'photo',
        'irrigation_system_id',
        'address',
        'rt',
        'rw',
        'area',
        'elevation',
        'gps_lat',
        'gps_long',
    ];

    // protected static function newFactory(): GreenhouseFactory
    // {
    //     // return GreenhouseFactory::new();
    // }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_id');
    }

    public function irrigationSystem()
    {
        return $this->belongsTo('Modules\FarmManagement\Models\IrrigationSystem', 'irrigation_system_id');
    }    

    public function getPhotoPathAttribute()
    {
        if($this->photo) return 'storage/img/photo/greenhouse/'.$this->photo;
        else return null;
    }

    public function getShowPhotoAttribute()
    {
        return $this->photoPath && Storage::disk('public')->exists('img/photo/greenhouse/'.$this->photo) ? $this->photoPath : 'img/avatar/default.png';
    }

    public function getGpsCoordinateAttribute()
    {
        return $this->gps_lat && $this->gps_lng ? $this->gps_lat.','.$this->gps_lng : null;
    }
}