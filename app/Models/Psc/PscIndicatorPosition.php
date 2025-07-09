<<<<<<< HEAD
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PscIndicatorPosition extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tm_psc_indicator_position";
    protected $fillable = [
    	'indicator_id',
    	'position_id',
    	'percentage'
    ];
    protected $dates = ['deleted_at'];

    public function indikator()
    {
        return $this->belongsTo('App\Models\Psc\PscIndicator','indicator_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }
}
=======
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PscIndicatorPosition extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tm_psc_indicator_position";
    protected $fillable = [
    	'indicator_id',
    	'position_id',
    	'percentage'
    ];
    protected $dates = ['deleted_at'];

    public function indikator()
    {
        return $this->belongsTo('App\Models\Psc\PscIndicator','indicator_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
