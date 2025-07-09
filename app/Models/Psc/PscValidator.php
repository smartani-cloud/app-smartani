<<<<<<< HEAD
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscValidator extends Model
{
    use HasFactory;

    protected $table = "psc_validator";
    protected $fillable = [
    	'position_desc',
    	'validator_name'
    ];

    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','validator_id');
    }
}
=======
<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PscValidator extends Model
{
    use HasFactory;

    protected $table = "psc_validator";
    protected $fillable = [
    	'position_desc',
    	'validator_name'
    ];

    public function scores()
    {
        return $this->hasMany('App\Models\Psc\PscScore','validator_id');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
