<<<<<<< HEAD
<?php

namespace App\Models\Psb;

use App\Models\Kbm\TahunAjaran;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterCounter extends Model
{
    use HasFactory;
    protected $table = "tm_register_counter";
    protected $fillable = [
        'unit_id',
        'academic_year_id',
        'student_status_id',
        'register_intern',
        'register_extern',
        'saving_seat_extern',
        'saving_seat_intern',
        'interview_intern',
        'interview_extern',
        'accepted_intern',
        'accepted_extern',
        'before_reapply_intern',
        'before_reapply_extern',
        'reapply_intern',
        'reapply_extern',
        'stored_intern',
        'stored_extern',
        'reserved_intern',
        'reserved_extern',
        'canceled_intern',
        'canceled_extern',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAjaran::class,'academic_year_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function statusSiswa()
    {
        return $this->belongsTo('App\Models\Siswa\StatusSiswa','student_status_id');
    }
}
=======
<?php

namespace App\Models\Psb;

use App\Models\Kbm\TahunAjaran;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterCounter extends Model
{
    use HasFactory;
    protected $table = "tm_register_counter";
    protected $fillable = [
        'unit_id',
        'academic_year_id',
        'student_status_id',
        'register_intern',
        'register_extern',
        'saving_seat_extern',
        'saving_seat_intern',
        'interview_intern',
        'interview_extern',
        'accepted_intern',
        'accepted_extern',
        'before_reapply_intern',
        'before_reapply_extern',
        'reapply_intern',
        'reapply_extern',
        'stored_intern',
        'stored_extern',
        'reserved_intern',
        'reserved_extern',
        'canceled_intern',
        'canceled_extern',
    ];

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAjaran::class,'academic_year_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }

    public function statusSiswa()
    {
        return $this->belongsTo('App\Models\Siswa\StatusSiswa','student_status_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
