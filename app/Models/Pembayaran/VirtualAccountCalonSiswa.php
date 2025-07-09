<?php

namespace App\Models\Pembayaran;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualAccountCalonSiswa extends Model
{
    use HasFactory;
    protected $table = "tm_candidate_virtual_account";
    protected $fillable = [
        'unit_id',
        'candidate_student_id',
        'spp_bank',
        'spp_va',
        'spp_trx_id',
        'bms_bank',
        'bms_va',
        'bms_trx_id',
    ];
    
    public function siswa()
    {
        return $this->belongsTo('App\Models\Siswa\CalonSiswa', 'candidate_student_id');
    }
}
