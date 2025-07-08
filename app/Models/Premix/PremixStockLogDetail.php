<?php

namespace App\Models\Premix;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PremixStockLogDetail extends Model
{
    use HasFactory;

    protected $table = "tm_premix_stock_log_details";
    protected $fillable = [
        'premix_stock_log_id',
        'material_supplier_id',
        'product_id',
    	'quantity'
    ];

    public function premixStockLog()
    {
        return $this->belongsTo('App\Models\Premix\PremixStockLog','premix_stock_log_id');
    }

    public function materialSupplier()
    {
        return $this->belongsTo('App\Models\Stock\MaterialSupplier','material_supplier_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product','product_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }

    public function scopeMaterialSupplier($query){
        return $query->whereNotNull('material_supplier_id')->whereNull('product_id');
    }

    public function scopeProduct($query){
        return $query->whereNull('material_supplier_id')->whereNotNull('product_id');
    }
}
