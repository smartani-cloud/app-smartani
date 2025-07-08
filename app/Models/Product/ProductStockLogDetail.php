<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockLogDetail extends Model
{
    use HasFactory;

    protected $table = "tm_product_stock_log_details";
    protected $fillable = [
        'product_stock_log_id',
        'material_supplier_id',
        'premix_id',
    	'quantity'
    ];

    public function productStockLog()
    {
        return $this->belongsTo('App\Models\Product\ProductStockLog','product_stock_log_id');
    }

    public function materialSupplier()
    {
        return $this->belongsTo('App\Models\Stock\MaterialSupplier','material_supplier_id');
    }

    public function premix()
    {
        return $this->belongsTo('App\Models\Premix\Premix','premix_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }

    public function scopeMaterialSupplier($query){
        return $query->whereNotNull('material_supplier_id')->whereNull('premix_id');
    }

    public function scopePremix($query){
        return $query->whereNull('material_supplier_id')->whereNotNull('premix_id');
    }
}
