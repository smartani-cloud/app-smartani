<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;
    protected $table = "tref_level";
    protected $fillable = ['level','unit_id','phase_id'];

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id');
    }
    
    public function phase()
    {
        return $this->belongsTo('App\Models\LevelPhase', 'phase_id');
    }

    public function siswa()
    {
        return $this->hasMany('App\Models\Siswa\Siswa');
    }

    public function indikatorIklas()
    {
        return $this->hasOne('App\Models\Penilaian\IndikatorIklas', 'level_id');
    }

    public function classes()
    {
        return $this->hasMany('App\Models\Kbm\Kelas','level_id');
    }

    public function mapelKelas()
    {
        return $this->hasMany('App\Models\Kbm\MapelKelas','level_id');
    }
    
    public function indikatorPengetahuan()
    {
        return $this->hasMany('App\Models\Penilaian\IndikatorPengetahuan', 'level_id');
    }

    public function curricula()
    {
        return $this->hasMany('App\Models\Kbm\TingkatKurikulum','level_id');
    }

    public function indikatorKurikulumIklas()
    {
        return $this->hasMany('App\Models\Penilaian\Iklas\IndikatorKurikulumIklas','level_id');
    }

    public function khatamTypes()
    {
        return $this->hasMany('App\Models\Penilaian\Kurdeka\LevelKhatamType','level_id');
    }
    
    public function objectives()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\Objective', 'level_id');
    }

    public function objectiveElements()
    {
        return $this->hasMany('App\Models\Penilaian\Tk\ObjectiveElement','level_id');
    }

    public function getRomanLevelAttribute(){
        $number = $this->level;
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public function getNextRomanLevelAttribute(){
        $number = $this->level+1;
        $map = array('M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1);
        $returnValue = '';
        while ($number > 0) {
            foreach ($map as $roman => $int) {
                if($number >= $int) {
                    $number -= $int;
                    $returnValue .= $roman;
                    break;
                }
            }
        }
        return $returnValue;
    }

    public function scopeNextLevel($query){
        return $query->where('id',$this->id+1);
    }
}
