<?php

namespace App\Helpers;

class NumberHelper{
    public static function getPenyebut($nilai) {
    	$nilai = abs($nilai);
    	$huruf = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
    	$temp = "";
    	if ($nilai < 12) {
    		$temp = " ". $huruf[$nilai];
    	} else if ($nilai <20) {
    		$temp = self::getPenyebut($nilai - 10). " belas";
    	} else if ($nilai < 100) {
    		$temp = self::getPenyebut($nilai/10)." puluh". self::getPenyebut($nilai % 10);
    	} else if ($nilai < 200) {
    		$temp = " seratus" . self::getPenyebut($nilai - 100);
    	} else if ($nilai < 1000) {
    		$temp = self::getPenyebut($nilai/100) . " ratus" . self::getPenyebut($nilai % 100);
    	} else if ($nilai < 2000) {
    		$temp = " seribu" . self::getPenyebut($nilai - 1000);
    	} else if ($nilai < 1000000) {
    		$temp = self::getPenyebut($nilai/1000) . " ribu" . self::getPenyebut($nilai % 1000);
    	} else if ($nilai < 1000000000) {
    		$temp = self::getPenyebut($nilai/1000000) . " juta" . self::getPenyebut($nilai % 1000000);
    	} else if ($nilai < 1000000000000) {
    		$temp = self::getPenyebut($nilai/1000000000) . " milyar" . self::getPenyebut(fmod($nilai,1000000000));
    	} else if ($nilai < 1000000000000000) {
    		$temp = self::getPenyebut($nilai/1000000000000) . " trilyun" . self::getPenyebut(fmod($nilai,1000000000000));
    	}     
    	return $temp;
    }
    
    public static function getTerbilang($nilai) {
    	if($nilai<0) {
    		$hasil = "minus ". trim(self::getPenyebut($nilai));
    	} else {
    		$hasil = trim(self::getPenyebut($nilai));
    	}     		
    	return $hasil;
    }
}