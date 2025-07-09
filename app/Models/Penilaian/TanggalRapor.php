<?php

namespace App\Models\Penilaian;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TanggalRapor extends Model
{
    use HasFactory;

    protected $table = "report_date";
    protected $guarded = [];

    public function semester()
    {
        return $this->belongsTo('App\Models\Kbm\Semester','semester_id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Models\Unit','unit_id');
    }

    public function scopePts($query){
        return $query->where('date_type',1);
    }

    public function scopePas($query){
        return $query->where('date_type',2);
    }
}
