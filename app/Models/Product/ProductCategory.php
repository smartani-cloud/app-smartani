<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = "tref_product_categories";
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany('App\Models\Product\Product','product_id');
    }
}
