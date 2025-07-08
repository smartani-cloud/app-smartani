<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    use HasFactory;

    protected $table = "tm_buyers";
    protected $fillable = [
    	'billing_to',
    	'billing_phone_number',
        'shipping_to',
        'shipping_address_1',
        'shipping_address_2',
        'shipping_postal_code',
        'shipping_phone_number',
        'unit_id',
    ];

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function sales()
    {
        return $this->hasMany('App\Models\Sale\Sale','buyer_id');
    }

    public function getNameAttribute()
    {
        return $this->billing_to.' ['.$this->billing_phone_number.']';
    }

    public function getBillingPhoneNumberIdAttribute()
    {
        $billing_phone_number = $this->billing_phone_number[0] == '0' ? '+62'.substr($this->billing_phone_number, 1) : $this->billing_phone_number;
        return $billing_phone_number;
    }

    public function getBillingPhoneNumberLocalAttribute()
    {
        $billing_phone_number = substr($this->billing_phone_number,0,3) == '+62' ? '0'.substr($this->billing_phone_number, 3) : $this->billing_phone_number;
        return $billing_phone_number;
    }

    public function getShippingAddressAttribute()
    {
        return $this->shipping_address_1.' '.$this->shipping_address_2;
    }

    public function getShippingPhoneNumberIdAttribute()
    {
        $shipping_phone_number = $this->shipping_phone_number[0] == '0' ? '+62'.substr($this->shipping_phone_number, 1) : $this->shipping_phone_number;
        return $shipping_phone_number;
    }

    public function getShippingPhoneNumberLocalAttribute()
    {
        $shipping_phone_number = substr($this->shipping_phone_number,0,3) == '+62' ? '0'.substr($this->shipping_phone_number, 3) : $this->shipping_phone_number;
        return $shipping_phone_number;
    }
}
