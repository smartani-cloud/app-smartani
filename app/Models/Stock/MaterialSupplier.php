<?php

namespace App\Models\Stock;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class MaterialSupplier extends Model
{
    use HasFactory;

    protected $table = "tas_material_supplier";
    protected $fillable = [
    	'material_id',
    	'supplier_id',
    	'moq',
    	'price',
    	'stock_quantity'
    ];

    public function material()
    {
        return $this->belongsTo('App\Models\Stock\Material','material_id');
    }

    public function supplier()
    {
        return $this->belongsTo('App\Models\Stock\Supplier','supplier_id');
    }

    public function priceLogs()
    {
        return $this->hasMany('App\Models\Stock\MaterialSupplierPriceLog','material_supplier_id');
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project\ProjectCogs','material_supplier_id');
    }

    public function productStockLogDetails()
    {
        return $this->hasMany('App\Models\Product\ProductStockLogDetail','material_supplier_id');
    }

    public function productStockTaxLogs()
    {
        return $this->hasMany('App\Models\Product\ProductStockTaxLog','material_supplier_id');
    }

    public function getNameAttribute()
    {
        return $this->material->name.' dari '.$this->supplier->name;
    }

    public function getNameUnitAttribute()
    {
        $unit = null;
        if($this->material->unit && $this->supplier->unit){
            $unit = ($this->material->unit_id == $this->supplier->unit_id) ? $this->material->unit->name : ($this->material->unit->name.' & '.$this->supplier->unit->name);
        }
        return $this->name.($unit ? ' - '.$unit : null);
    }

    public function getMoqWithSeparatorAttribute()
    {
        return number_format($this->moq, 0, ',', '.');
    }

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    public function getStockQuantityWithSeparatorAttribute()
    {
        return number_format($this->stock_quantity, 0, ',', '.');
    }

    public function getNameWithStockAttribute()
    {
        return $this->name.' ['.$this->stockQuantityWithSeparator.']';
    }

    public function getNameUnitWithStockAttribute()
    {
        return $this->nameUnit.' ['.$this->stockQuantityWithSeparator.']';
    }

    public function getMoqPriceAttribute()
    {
        return $this->moq*$this->price;
    }

    public function getMoqPriceWithSeparatorAttribute()
    {
        return number_format($this->moqPrice, 0, ',', '.');
    }

    public function getCreatedAtIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->created_at)->format('j F Y H.i');
    }

    public function getCreatedAtIdShortAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->created_at)->format('j M Y H.i');
    }

    public function scopeOwnUnit($query,$unit){
        if($unit){
            return $query->whereHas('material',function($q)use($unit){
                $q->where('unit_id',$unit);
            })->whereHas('supplier',function($q)use($unit){
                $q->where('unit_id',$unit);
            });
        }
        else return $query;
    }
}
