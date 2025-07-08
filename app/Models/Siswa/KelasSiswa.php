<?php

namespace App\Models\Siswa;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasSiswa extends Model
{
    use HasFactory;
    protected $table = "student_class";
    protected $fillable = ['class_id','student_id','semester_id'];

    public function kelases()
    {
        return $this->belongsTo('App\Models\Kbm\Kelas','class_id');
    }
}
