<?php

use App\Models\Kbm\TahunAjaran;

function getAcademicYearActive() {
    $now = TahunAjaran::where('is_active',1)->first();
    return $now;
}

function yearList() {

    $years = [];

    for($init_year = date("Y"); $init_year >= 2021; $init_year--){
        array_push($years, $init_year);
    }
    
    return $years;
}

function monthList() {
    
    $months = array();

    for($i = 0; $i < 12; $i++){
        $obj = new stdClass();
        if($i == 0){}
        switch ($i) {
            case 0:
                $obj->id = '01';
                $obj->name = 'Januari';
                break;
            case 1:
                $obj->id = '02';
                $obj->name = 'Februari';
                break;
            case 2:
                $obj->id = '03';
                $obj->name = 'Maret';
                break;
            case 3:
                $obj->id = '04';
                $obj->name = 'April';
                break;
            case 4:
                $obj->id = '05';
                $obj->name = 'Mei';
                break;
            case 5:
                $obj->id = '06';
                $obj->name = 'Juni';
                break;
            case 6:
                $obj->id = '07';
                $obj->name = 'Juli';
                break;
            case 7:
                $obj->id = '08';
                $obj->name = 'Agustus';
                break;
            case 8:
                $obj->id = '09';
                $obj->name = 'September';
                break;
            case 9:
                $obj->id = '10';
                $obj->name = 'Oktober';
                break;
            case 10:
                $obj->id = '11';
                $obj->name = 'November';
                break;
            case 11:
                $obj->id = '12';
                $obj->name = 'Desember';
                break;
        }
        array_push($months, $obj);

    }

    return $months;
    
}

function academicMonthList() {
    
    $months = array();
	
	$monthIds = ['7','8','9','10','11','12','1','2','3','4','5','6'];
    foreach($monthIds as $i){
        $obj = new stdClass();
		if($i-1 == 0){}
        switch ($i-1) {
            case 0:
                $obj->id = '01';
                $obj->name = 'Januari';
                break;
            case 1:
                $obj->id = '02';
                $obj->name = 'Februari';
                break;
            case 2:
                $obj->id = '03';
                $obj->name = 'Maret';
                break;
            case 3:
                $obj->id = '04';
                $obj->name = 'April';
                break;
            case 4:
                $obj->id = '05';
                $obj->name = 'Mei';
                break;
            case 5:
                $obj->id = '06';
                $obj->name = 'Juni';
                break;
            case 6:
                $obj->id = '07';
                $obj->name = 'Juli';
                break;
            case 7:
                $obj->id = '08';
                $obj->name = 'Agustus';
                break;
            case 8:
                $obj->id = '09';
                $obj->name = 'September';
                break;
            case 9:
                $obj->id = '10';
                $obj->name = 'Oktober';
                break;
            case 10:
                $obj->id = '11';
                $obj->name = 'November';
                break;
            case 11:
                $obj->id = '12';
                $obj->name = 'Desember';
                break;
        }
        array_push($months, $obj);

    }

    return $months;
    
}

function monthText($month) {

    $obj = new stdClass();
    $i = $month - 1;
        switch ($i) {
            case 0:
                $obj->name = 'Januari';
                break;
            case 1:
                $obj->name = 'Februari';
                break;
            case 2:
                $obj->name = 'Maret';
                break;
            case 3:
                $obj->name = 'April';
                break;
            case 4:
                $obj->name = 'Mei';
                break;
            case 5:
                $obj->name = 'Juni';
                break;
            case 6:
                $obj->name = 'Juli';
                break;
            case 7:
                $obj->name = 'Agustus';
                break;
            case 8:
                $obj->name = 'September';
                break;
            case 9:
                $obj->name = 'Oktober';
                break;
            case 10:
                $obj->name = 'November';
                break;
            case 11:
                $obj->name = 'Desember';
                break;
        }

    return $obj->name;
    
}

function getMonthNow(){

    return date("m");

}

function getYearNow(){

    return date("Y");

}

function getAcademicYearList()
{
    $year_active = TahunAjaran::where('is_active',1)->first();
    $academic_years = TahunAjaran::where('id', '<=', $year_active->id+2)->orderBy('academic_year_start','desc')->get();
    return $academic_years;
}