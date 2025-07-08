<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectCogs extends Model
{
    use HasFactory;

    protected $table = "project_cogs";
    protected $fillable = [
    	'project_id',
    	'material_supplier_id',
        'quantity_purpose',
        'quantity_buy',
        'nominal',
        'nominal_moq'
    ];

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function materialSupplier()
    {
        return $this->belongsTo('App\Models\Stock\MaterialSupplier','material_supplier_id');
    }

    public function getQuantityPurposeWithSeparatorAttribute()
    {
        return number_format($this->quantity_purpose, 0, ',', '.');
    }

    public function getQuantityBuyWithSeparatorAttribute()
    {
        return number_format($this->quantity_buy, 0, ',', '.');
    }

    public function getNominalWithSeparatorAttribute()
    {
        return number_format($this->nominal, 0, ',', '.');
    }

    public function getNominalMoqWithSeparatorAttribute()
    {
        return number_format($this->nominal_moq, 0, ',', '.');
    }

    public function getPriceAttribute()
    {
        $price = $this->project->acc_status == 1 ? ($this->quantity_purpose  > 0 ? ($this->nominal/$this->quantity_purpose) : 0) : $this->materialSupplier->price;
        return $price;
    }

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }
}
