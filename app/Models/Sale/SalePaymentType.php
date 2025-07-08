<?php

namespace App\Models\Sale;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalePaymentType extends Model
{
    use HasFactory;

    protected $table = "tref_sale_payment_types";
    protected $fillable = ['name'];

    public function sales()
    {
        return $this->hasMany('App\Models\Sale\Sale','payment_type_id');
    }
}
