<?php

namespace App\Models\Psc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PscGradeSet extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tref_psc_grade_set";
    protected $fillable = ['name','status_id'];
    protected $dates = ['deleted_at'];

    public function status()
    {
        return $this->belongsTo('App\Models\StatusAktif','status_id');
    }

    public function grade()
    {
        return $this->hasMany('App\Models\Psc\PscGrade','set_id');
    }

    public function scopeAktif($query){
        return $query->where('status_id',1);
    }

    public function getGradeSortedAttribute(){
        return $this->grade()->select('name')->orderBy('end','desc')->pluck('name');
    }

    public function getIsEditableAttribute(){
        return $this->grade()->whereHas('scores',function($q){$q->where('acc_status_id',1);})->count() > 0 ? false : true;
    }
}
