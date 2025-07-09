<?php

namespace App\Models\Import;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportVirtualAccount extends Model
{
    use HasFactory;
    protected $table = "import_va";
    protected $fillable = [
        'student_id',
        'success',
        'siswa_id',
        'siswa_nipd',
    ];

    
}
