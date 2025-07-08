<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = "tm_products";
    protected $fillable = [
    	'name',
    	'sku_number',
        'unit_id',
        'product_category_id'
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Product\ProductCategory','product_category_id');
    }

    public function salesTypes()
    {
        return $this->hasMany('App\Models\Product\ProductSalesType','product_id');
    }

    public function stocks()
    {
        return $this->hasMany('App\Models\Product\ProductStock','product_id');
    }

    public function projectProductCogs()
    {
        return $this->hasMany('App\Models\Project\ProjectProductCogs','product_id');
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

    public function getProductNameAttribute()
    {
        return $this->name.' - '.$this->unit->name.' - '.$this->category->name.($this->sku_number ? ' - '.$this->sku_number : '');
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
