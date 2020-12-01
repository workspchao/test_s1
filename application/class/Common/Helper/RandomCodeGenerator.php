<?php

namespace Common\Helper;

class RandomCodeGenerator
{

    /**
     * 
     * @param type $n - length
     * @param type $type - type (num, char)
     * @return type
     */
    public static function generate($n = 5, $type = null, $upper = FLAG_YES)
    {
        $array = null;
        if($type == 'num'){
            $array = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        }
        else if($type == 'char'){
            $array = array("a", "b", "c", "d", "e", "f", "g", "h",
                //"i", 
                "j", "k",
                //"l",
                "m", "n",
                //"o",
                "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"
            );
        }
        else{
            $array = array(
                "1","2","3","4","5","6","7","8","9",
                "a","b","c","d","e","f","g",
                "h",    "j","k",    "m","n",
                    "p","q","r","s","t",
                "u","v","w","x","y"
            );
        }
        
        shuffle($array);
        $length = count($array) - 1;
        $chars  = '';
        for ($i = 0; $i < $n; $i++) {
            $rand = mt_rand(0, $length);
            $char = $array[$rand];

            if($upper == FLAG_YES){
                if(($rand) % 2 == 0){
                    $char = strtoupper($char);
                }
            }
            
            $chars .= $char;
        }
        return $chars;
    }
}
