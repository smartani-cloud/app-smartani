<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProductSalesType extends Model
{
    use HasFactory;

    protected $table = "project_product_sales_type";
    protected $fillable = [
    	'project_id',
    	'product_sales_type_id',
        'quantity',
        'nominal',
        'percentage'
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function productSalesType()
    {
        return $this->belongsTo('App\Models\Product\ProductSalesType','product_sales_type_id');
    }

    public function getQuantityWithSeparatorAttribute()
    {
        return number_format($this->quantity, 0, ',', '.');
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getPriceAttribute()
    {
        $price = $this->project->acc_status == 1 ? ($this->quantity  > 0 ? ($this->nominal/$this->quantity) : 0) : $this->productSalesType->priceWithSeparator;
        return $price;
    }

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }
}
