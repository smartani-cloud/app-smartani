<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestCallback extends Model
{
    use HasFactory;
    protected $table = "test_callback";
    protected $fillable = [
        'code',
        'message',
        'type',
        'call_id',
        'number',
        'amount',
        'remaining_amount',
        'virtual_account',
        'va',
        'date',
        'bank_code',
        'bank_name',
        'ref',
        'channel',
        'email',
        'address',
        'transaction_id',
    ];
}
