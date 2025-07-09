<?php

namespace App\Models\Penempatan;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriJabatan extends Model
{
    use HasFactory;

    protected $table = "tref_position_category";

    public function jabatan()
    {
        return $this->hasMany('App\Models\Penempatan\Jabatan','category_id');
    }

    public function getAcronymAttribute(){
        if($this->name){
            $words = explode(" ", $this->name);
            $acronym = "";

            foreach ($words as $w) {
              $acronym .= $w[0];
            }

            return $acronym;
        }
        else return null;
    }
}
