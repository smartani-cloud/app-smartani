<<<<<<< HEAD
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = "tref_jobs";

    public $timestamps = false;
    
    public function scopeActive($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('is_active',0);
    }
}
=======
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $table = "tref_jobs";

    public $timestamps = false;
    
    public function scopeActive($query)
    {
        return $query->where('is_active',1);
    }
    
    public function scopeInactive($query)
    {
        return $query->where('is_active',0);
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
