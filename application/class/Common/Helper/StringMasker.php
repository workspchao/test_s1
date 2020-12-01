<?php

namespace Common\Helper;

class StringMasker
{
    /**
     * 
     * @param type $ori_string
     * @param type $type (center, both, left, right)
     * @param type $num
     * @param type $num2
     * @param type $mask_ch
     */
    public static function mask($ori_string, $type, $num, $num2, $mask_ch = '*', $reverse = false){
        
        if(!$ori_string){
            return $ori_string;
        }
        
        $len = strlen($ori_string);
        $pre_str = '';
        $mid_str = '';
        $end_str = '';
        
        if($type == "center"){
            if($num >= $len || $num2 >= $len){
                $num = 0;
                $mask_len = $len;
            }
            else{
                $mask_len = $len - $num - $num2;
            }
            
            if($reverse){
                $pre_str = str_repeat($mask_ch, $num);
                $mid_str = substr($ori_string, $num, $mask_len);
                $end_str = str_repeat($mask_ch, $num2);
            }
            else{
                $pre_str = substr($ori_string, 0, $num);
                $mid_str = str_repeat($mask_ch, $mask_len);
                $end_str = substr($ori_string, $num + $mask_len);
            }
            
        }
        else if($type == "both"){
            if($num >= $len || $num2 >= $len){
                $num = 0;
                $mask_len = $len;
            }
            else{
                $mask_len = $len - $num - $num2;
            }
            
            if($reverse){
                $pre_str = substr($ori_string, 0, $num);
                $mid_str = str_repeat($mask_ch, $mask_len);
                $end_str = substr($ori_string, $num + $mask_len);
            }
            else{
                $pre_str = str_repeat($mask_ch, $num);
                $mid_str = substr($ori_string, $num, $mask_len);
                $end_str = str_repeat($mask_ch, $num2);
            }
        }
        else if($type == "left"){
            if($num >= $len){
                $num = 0;
                $mask_len = $len;
            }
            else{
                $mask_len = $len - $num;
            }
            
            $pre_str = str_repeat($mask_ch, $mask_len);
            $end_str = substr($ori_string, $mask_len);
        }
        else if($type == "right"){
            if($num >= $len){
                $num = 0;
                $mask_len = $len;
            }
            else{
                $mask_len = $len - $num;
            }
            
            $pre_str = substr($ori_string, 0, $num);
            $end_str = str_repeat($mask_ch, $mask_len);
        }
        else{
            $end_str = str_repeat($mask_ch, $len);
        }
        
        return $pre_str . $mid_str . $end_str;
    }
//    
//    public static function mask($ori_string, $number_of_digits, $mask_ch = '*')
//    {
//        $len = strlen($ori_string);
//        if ($number_of_digits >= $len) {
//            $mask_len = $len;
//            $end_str = '';
//        }
//        else {
//            $mask_len = $len - $number_of_digits;
//            $end_str = substr($ori_string, $mask_len, $number_of_digits);
//        }
//
//        return str_repeat($mask_ch, $mask_len) . $end_str;
//    }
}
