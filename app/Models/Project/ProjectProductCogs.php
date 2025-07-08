<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectProductCogs extends Model
{
    use HasFactory;

    protected $table = "project_product_cogs";
    protected $fillable = [
    	'project_id',
    	'product_id',
        'nominal',
        'percentage',
        'desc'
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product\Product','product_id');
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }
}
