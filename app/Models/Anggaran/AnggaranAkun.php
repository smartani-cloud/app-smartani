<?php

namespace App\Models\Anggaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AnggaranAkun extends Pivot
{
    use HasFactory;

    protected $table = "budgeting_account";
    protected $fillable = [
    	'budgeting_budgeting_type_id',
    	'account_id'
    ];

    public function jenisAnggaranAnggaran()
    {
        return $this->belongsTo('App\Models\Anggaran\JenisAnggaranAnggaran','budgeting_budgeting_type_id');
    }

    public function akun()
    {
        return $this->belongsTo('App\Models\Anggaran\Akun','account_id');
    }
}
