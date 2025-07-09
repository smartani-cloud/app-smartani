<?php

namespace App\Models\Iku;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IkuAspect extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tm_iku_aspects";
    protected $fillable = [
    	'iku_category_id',
    	'name'
    ];
    protected $dates = ['deleted_at'];

    public function kategori()
    {
        return $this->belongsTo('App\Models\Iku\IkuCategory','iku_category_id');
    }

    public function jabatan()
    {
        return $this->hasMany('App\Models\Iku\IkuAspectPosition','iku_aspect_id');
    }

    public function unit()
    {
        return $this->hasMany('App\Models\Iku\IkuAspectUnit','iku_aspect_id');
    }
}
