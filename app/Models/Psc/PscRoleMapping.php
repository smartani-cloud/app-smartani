<<<<<<< HEAD
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscRoleMapping extends Model
{
    use HasFactory;

    protected $table = "tm_psc_role_mapping";
    protected $fillable = [
    	'target_position_id',
    	'pa_role_mapping_id',
    	'position_id'
    ];

    public function target()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','target_position_id');
    }

    public function peran()
    {
        return $this->belongsTo('App\Models\Psc\PaRoleMapping','pa_role_mapping_id');
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

class PscRoleMapping extends Model
{
    use HasFactory;

    protected $table = "tm_psc_role_mapping";
    protected $fillable = [
    	'target_position_id',
    	'pa_role_mapping_id',
    	'position_id'
    ];

    public function target()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','target_position_id');
    }

    public function peran()
    {
        return $this->belongsTo('App\Models\Psc\PaRoleMapping','pa_role_mapping_id');
    }

    public function jabatan()
    {
        return $this->belongsTo('App\Models\Penempatan\Jabatan','position_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
