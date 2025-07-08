<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockLog extends Model
{
    use HasFactory;

    protected $table = "tm_product_stock_logs";
    protected $fillable = [
        'product_id',
    	'quantity'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product','product_id');
    }

    public function details()
    {
        return $this->hasMany('App\Models\Product\ProductStockLogDetail','product_stock_log_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }
}
