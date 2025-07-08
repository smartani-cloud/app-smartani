<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStock extends Model
{
    use HasFactory;

    protected $table = "tm_product_stocks";
    protected $fillable = [
        'product_id',
    	'year',
    	'quantity',
        'used',
        'taxed',
        'sold',
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product','product_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }

    public function getUsedWithSeparatorAttribute()
    {
        return number_format($this->used, 0, ',', '.');
    }

    public function getTaxedWithSeparatorAttribute()
    {
        return number_format($this->taxed, 0, ',', '.');
    }

    public function getSoldWithSeparatorAttribute()
    {
        return number_format($this->sold, 0, ',', '.');
    }
}
