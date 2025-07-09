<<<<<<< HEAD
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = "tref_statuses";

    protected $fillable = ['code', 'name', 'category'];
    
    public $timestamps = false;

    public static function byCategory(string $category)
    {
        return static::where('category', $category)->get();
    }

    public static function optionsFor(string $category)
    {
        return static::where('category', $category)->pluck('name', 'id');
    }

    public static function id(string $code, string $category)
    {
        return static::where('code', $code)->where('category', $category)->value('id');
    }
=======
<?php

namespace Modules\Core\Models\References;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $table = "tref_statuses";

    protected $fillable = ['code', 'name', 'category'];
    
    public $timestamps = false;

    public static function byCategory(string $category)
    {
        return static::where('category', $category)->get();
    }

    public static function optionsFor(string $category)
    {
        return static::where('category', $category)->pluck('name', 'id');
    }

    public static function id(string $code, string $category)
    {
        return static::where('code', $code)->where('category', $category)->value('id');
    }
>>>>>>> 519c7866245bb7df43bd5924d819bc4ab649e1f7
}