<<<<<<< HEAD
<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhatamBuku extends Model
{
    use HasFactory;
    protected $table = "rkd_khatam_books";
    protected $fillable = [
        'report_score_id',
		'book_id',
    ];
	
    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }

    public function buku()
    {
        return $this->belongsTo('App\Models\Penilaian\Kurdeka\Buku', 'book_id');
    }
}
=======
<?php

namespace App\Models\Penilaian\Kurdeka;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhatamBuku extends Model
{
    use HasFactory;
    protected $table = "rkd_khatam_books";
    protected $fillable = [
        'report_score_id',
		'book_id',
    ];
	
    public function rapor()
    {
        return $this->belongsTo('App\Models\Penilaian\NilaiRapor', 'report_score_id');
    }

    public function buku()
    {
        return $this->belongsTo('App\Models\Penilaian\Kurdeka\Buku', 'book_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
