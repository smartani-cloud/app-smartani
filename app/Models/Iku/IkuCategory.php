<?php

namespace App\Models\Iku;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkuCategory extends Model
{
    use HasFactory;

    protected $table = "tref_iku_category";
    protected $fillable = ['name'];

    public function aspek()
    {
        return $this->hasMany('App\Models\Iku\IkuAspect','iku_category_id');
    }

    public function nilai()
    {
        return $this->hasMany('App\Models\Iku\IkuAchievement','iku_category_id');
    }

    public function getNameLcAttribute(){
    	return strtolower($this->name);
    }
}
