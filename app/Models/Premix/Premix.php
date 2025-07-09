<?php

namespace App\Models\Premix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Premix extends Model
{
    use HasFactory;

    protected $table = "tm_premixes";
    protected $fillable = [
    	'name',
        'unit_id',
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function stocks()
    {
        return $this->hasMany('App\Models\Premix\PremixStock','premix_id');
    }

    public function productStockLogDetails()
    {
        return $this->hasMany('App\Models\Product\ProductStockLogDetail','material_supplier_id');
    }

    public function getStockQuantityAttribute()
    {
        return $this->stocks()->sum('quantity');
    }

    public function getStockQuantityWithSeparatorAttribute()
    {
        return number_format($this->stockQuantity, 0, ',', '.');
    }

    public function getNameUnitAttribute()
    {
        return $this->name.($this->unit ? ' - '.$this->unit->name : null);
    }

    public function getNameWithStockAttribute()
    {
        return $this->name.' ['.$this->stockQuantityWithSeparator.']';
    }

    public function getNameUnitWithStockAttribute()
    {
        return $this->nameUnit.' ['.$this->stockQuantityWithSeparator.']';
    }
}
