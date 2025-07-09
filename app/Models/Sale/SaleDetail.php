<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $table = "tm_sale_details";
    protected $fillable = [
    	'sales_id',
    	'product_sales_type_id',
        'price',
        'quantity',
        'subtotal',
    ];

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale\Sale','sale_id');
    }

    public function productSalesType()
    {
        return $this->belongsTo('App\Models\Product\ProductSalesType','product_sales_type_id');
    }

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }

    public function getSubtotalWithSeparatorAttribute()
    {
        return number_format($this->subtotal, 0, ',', '.');
    }
}
