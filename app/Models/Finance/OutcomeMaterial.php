<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OutcomeMaterial extends Model
{
    use HasFactory;

    protected $table = "outcome_materials";
    protected $fillable = [
    	'outcome_id',
    	'material_supplier_id',
        'quantity_propose',
        'quantity_buy',
        'amount',
        'amount_moq'
    ];

    public function outcome()
    {
        return $this->belongsTo('App\Models\Finance\Outcome','outcome_id');
    }

    public function materialSupplier()
    {
        return $this->belongsTo('App\Models\Stock\MaterialSupplier','material_supplier_id');
    }

    public function bill()
    {
        return $this->hasOne('App\Models\Finance\OutcomeMaterialBill','outcome_material_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Finance\OutcomeMaterialPayment','outcome_material_id');
    }

    public function getQuantityProposeWithSeparatorAttribute()
    {
        return number_format($this->quantity_propose, 0, ',', '.');
    }

    public function getQuantityBuyWithSeparatorAttribute()
    {
        return number_format($this->quantity_buy, 0, ',', '.');
    }

    public function getAmountWithSeparatorAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }

    public function getAmountMoqWithSeparatorAttribute()
    {
        return number_format($this->amount_moq, 0, ',', '.');
    }

    public function getPaidAttribute()
    {
        return $this->bill ? $this->bill->paid : 0;
    }

    public function getPaidWithSeparatorAttribute()
    {
        return $this->bill ? $this->bill->paidWithSeparator : 0;
    }

    public function getRemainAttribute()
    {
        return $this->bill ? $this->bill->remain : $this->amount_moq;
    }

    public function getRemainWithSeparatorAttribute()
    {
        return $this->bill ? $this->bill->remainWithSeparator : $this->AmountMoqWithSeparator;
    }

    public function getStatusAttribute()
    {
        return $this->bill ? $this->bill->status : 'Belum Bayar';
    }

    public function getStatusBadgeAttribute()
    {
        return $this->bill ? $this->bill->statusBadge : '<span class="badge badge-danger">'.$this->status.'</span>';
    }

    public function getPriceAttribute()
    {
        $price = $this->outcome->acc_status == 1 || $this->outcome->type_id == 3 ? ($this->quantity_propose  > 0 ? ($this->amount/$this->quantity_propose) : 0) : $this->materialSupplier->price;
        return $price;
    }

    public function getPriceWithSeparatorAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }
}
