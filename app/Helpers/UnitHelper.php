<?php

use App\Models\Level;
use App\Models\Unit;

function getLevels ($unit_id){

  $levels = Level::where('unit_id', $unit_id)->get();
  return json_encode($levels);

}

function getUnits (){

  $unit_id = auth()->user()->pegawai->unit_id;

  if($unit_id == 5){
    $units = Unit::where('is_school', 1)->get();
  }else{
    $units = Unit::where('id', $unit_id)->get();
  }

  return $units;

}