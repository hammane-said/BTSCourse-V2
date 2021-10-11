<?php 
namespace App\Helper;

class Helper
{
    static function IsNullOrEmptyString($str){
        return (!isset($str) || trim($str) === '');
    }
}


?>