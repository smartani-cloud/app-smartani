<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BenerinDate extends Model
{
    use HasFactory;
    
    protected $table = "benerin_date";

    protected $fillable = [
        'siswa_id',
        'siswa_nipd',
    ];
}
