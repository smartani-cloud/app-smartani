<<<<<<< HEAD
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleGroup extends Model
{
    use HasFactory;

    protected $table = "tref_user_role_group";

    public function roles()
    {
        return $this->hasMany('App\Models\Role','role_group_jd');
    }
}
=======
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleGroup extends Model
{
    use HasFactory;

    protected $table = "tref_user_role_group";

    public function roles()
    {
        return $this->hasMany('App\Models\Role','role_group_jd');
    }
}
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
