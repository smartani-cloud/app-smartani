<?php

namespace App\Helpers;

class PhoneHelper{
    public static function addDashesId($number,$category='mobile',$area=0){
        $dashedNumber = null;
        if(strlen($number) > 2 && (substr($number, 0, 2) == "62" || substr($number, 0, 1) == "0")){
            $phoneNumber = substr($number, 0, 2) == "62" ? substr($number, 2) : substr($number, 1);
            $digit = strlen($phoneNumber);
            if($category == 'mobile'){
                if($digit == 9){
                    $dashedNumber = ($area == 62 ? "+62 ":"0").substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 3) . "-" . substr($phoneNumber, 6, 3);
                }
                if($digit == 10){
                    $dashedNumber = ($area == 62 ? "+62 ":"0").substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7, 3);
                }
                if($digit == 11){
                    $dashedNumber = ($area == 62 ? "+62 ":"0").substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7, 4);
                }
                if($digit == 12){
                    $dashedNumber = ($area == 62 ? "+62 ":"0").substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7, 5);
                }
            }
            elseif($category == 'home'){
                if($digit == 9){
                    $dashedNumber = ($area == 62 ? "+62 ":"0").substr($phoneNumber, 0, 2) . "-" . substr($phoneNumber, 2, 4) . "-" . substr($phoneNumber, 6, 3);
                }
                if($digit == 10){
                    $dashedNumber = ($area == 62 ? "+62 ":"0").substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7, 3);
                }
                if($digit == 11){
                    $dashedNumber = ($area == 62 ? "+62 ":"0").substr($phoneNumber, 0, 3) . "-" . substr($phoneNumber, 3, 4) . "-" . substr($phoneNumber, 7, 4);
                }
            }
        }
        
        return $dashedNumber ? $dashedNumber : $number;
    }
}