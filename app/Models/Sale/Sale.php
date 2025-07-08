<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Jenssegers\Date\Date;

class Sale extends Model
{
    use HasFactory;

    protected $table = "tm_sales";
    protected $fillable = [
    	'date',
        'buyer_id',
    	'unit_id',
        'project_id',
        'payment_type_id',
        'due_date',
        'total_amount',
        'tax_percentage',
        'balance',
        'refund',
        'sale_id',
        'user_id',
        'is_active',
    ];

    public function buyer()
    {
        return $this->belongsTo('App\Models\Sale\Buyer','buyer_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function project()
    {
        return $this->belongsTo('App\Models\Project\Project','project_id');
    }

    public function paymentType()
    {
        return $this->belongsTo('App\Models\Sale\SalePaymentType','payment_type_id');
    }

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale\Sale','sale_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id');
    }

    public function details()
    {
        return $this->hasMany('App\Models\Sale\SaleDetail','sale_id');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Sale\SalePayment','sale_id');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale\Sale','sale_id');
    }

    public function getNameAttribute()
    {
        return Date::parse($this->date)->format('Ymj').$this->id;
    }

    public function getDateIdAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('j F Y');
    }

    public function getDatedmYAttribute()
    {
        Date::setLocale('id');
        return Date::parse($this->date)->format('d-m-Y');
    }

    public function getDueDateIdAttribute()
    {
        Date::setLocale('id');
        return $this->due_date ? Date::parse($this->due_date)->format('j F Y') : null;
    }

    public function getDueDatedmYAttribute()
    {
        Date::setLocale('id');
        return $this->due_date ? Date::parse($this->due_date)->format('d-m-Y') : null;
    }

    public function getStatusAttribute()
    {
        if($this->paymentType){
            if($this->paymentType->name == 'Berkala'){
                if($this->payments()->sum('value') <= 0)
                    return "Belum Bayar";
                elseif($this->payments()->sum('value') < $this->total_amount)
                    return "Bayar Sebagian";
                else
                    return "Sudah Lunas";

            }
            else return "Sudah Lunas";
        }
        else{
            if($this->is_active == 0) return "Sudah Lunas";
            else return "Draf";
        }
    }

    public function getStatusBadgeAttribute()
    {
        if($this->status == 'Sudah Lunas'){
            return '<span class="badge badge-success">'.$this->status.'</span>';
        }
        elseif($this->status == 'Bayar Sebagian'){
            return '<span class="badge badge-warning">'.$this->status.'</span>';
        }
        elseif($this->status == 'Belum Bayar'){
            return '<span class="badge badge-danger">'.$this->status.'</span>';
        }
        else{
            return '<span class="badge badge-secondary">'.$this->status.'</span>';
        }
    }

    public function getTotalAmountWithSeparatorAttribute()
    {
        return number_format($this->total_amount, 0, ',', '.');
    }

    public function getBalanceWithSeparatorAttribute()
    {
        return number_format($this->balance, 0, ',', '.');
    }

    public function getRefundWithSeparatorAttribute()
    {
        return number_format($this->refund, 0, ',', '.');
    }

    public function getTaxAttribute()
    {
        return $this->total_amount*($this->tax_percentage/100);
    }

    public function getTaxWithSeparatorAttribute()
    {
        return number_format($this->tax, 0, ',', '.');
    }

    public function getNetTotalAmountAttribute()
    {
        return $this->total_amount-$this->tax;
    }

    public function getNetTotalAmountWithSeparatorAttribute()
    {
        return number_format($this->netTotalAmount, 0, ',', '.');
    }

    public function getBillAttribute()
    {
        if($this->paymentType){
            if($this->paymentType->name == 'Berkala'){
                if($this->payments()->sum('value') <= 0 || ($this->payments()->sum('value') < $this->total_amount))
                    return $this->total_amount-$this->payments()->sum('value');
                else
                    return 0;

            }
            else return 0;
        }
        else{
            if($this->is_active == 0) return 0;
            else return $this->total_amount;
        }
    }

    public function getBillWithSeparatorAttribute()
    {
        return number_format($this->bill, 0, ',', '.');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('is_active',0);
    }
    
    public function updateTotalAmount()
    {
        $this->total_amount = $this->details()->sum('subtotal');
        $this->save();
    }
}
