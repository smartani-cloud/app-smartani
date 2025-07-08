<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSalesType extends Model
{
    use HasFactory;

    protected $table = "tas_product_sales_type";
    protected $fillable = [
    	'product_id',
    	'sales_type_id',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product','product_id');
    }

    public function salesType()
    {
        return $this->belongsTo('App\Models\Sale\SalesType','sales_type_id');
    }

    public function projects()
    {
        return $this->hasMany('App\Models\Project\ProjectProductSalesType','product_sales_type_id');
    }

    public function getNameAttribute()
    {
        return $this->product->productName.' ['.$this->salesType->name.']';
    }

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }
}
