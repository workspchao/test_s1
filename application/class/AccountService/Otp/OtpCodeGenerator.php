<?php

namespace AccountService\Otp;

class OtpCodeGenerator{

    public static function generate($length = 6)
    {
        $digits  = '';
        $numbers = range(0,9);

        shuffle($numbers);

        for($i=0; $i<$length; $i++)
        {
            $digits .= $numbers[$i];
        }

        return $digits;
    }
}