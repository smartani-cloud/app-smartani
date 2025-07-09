<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesType extends Model
{
    use HasFactory;

    protected $table = "tref_sales_types";
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany('App\Models\Product\ProductSalesType','sales_type_id');
    }
}
