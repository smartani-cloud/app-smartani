<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeMaterialBill extends Model
{
    use HasFactory;

    protected $table = "outcome_material_bills";
    protected $fillable = [
    	'outcome_material_id',
        'paid',
        'remain',
    ];

    public function outcomeMaterial()
    {
        return $this->belongsTo('App\Models\Finance\OutcomeMaterial','outcome_material_id');
    }

    public function getPaidWithSeparatorAttribute()
    {
        return number_format($this->paid, 0, ',', '.');
    }

    public function getRemainWithSeparatorAttribute()
    {
        return number_format($this->remain, 0, ',', '.');
    }

    public function getStatusAttribute()
    {
        if($this->paid <= 0)
            return "Belum Bayar";
        elseif(($this->paid > 0) && ($this->remain > 0))
            return "Bayar Sebagian";
        else
            return "Sudah Lunas";
    }

    public function getStatusBadgeAttribute()
    {
        if($this->status == 'Sudah Lunas'){
            return '<span class="badge badge-success">'.$this->status.'</span>';
        }
        elseif($this->status == 'Bayar Sebagian'){
            return '<span class="badge badge-warning">'.$this->status.'</span>';
        }
        else{
            return '<span class="badge badge-danger">'.$this->status.'</span>';
        }
    }
}
