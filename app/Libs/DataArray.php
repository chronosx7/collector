<?php

namespace App\Libs;

class DataArray{
    public static function has_non_empty_key(&$data, $key){
        $res = false;
        if(array_key_exists($key, $data) && $data[$key] != ''){
            $res = true;
        }
        return $res;
    }
    
    public static function release_null_values(&$data){
        foreach($data as $key => $value){
            if($value == 'null'){
                unset($data[$key]);
            }
        }
    }
    
    public static function key_has_value(&$data, $key, $value){
        $res = false;
        if(self::has_non_empty_key($data, $key) && $data[$key] == $value){
            $res = true;
        }
        return $res;
    }
}