<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockTaxLog extends Model
{
    use HasFactory;

    protected $table = "tm_product_stock_tax_logs";
    protected $fillable = [
        'product_id',
        'material_supplier_id',
    	'quantity'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product','product_id');
    }

    public function materialSupplier()
    {
        return $this->belongsTo('App\Models\Stock\MaterialSupplier','material_supplier_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }
}
