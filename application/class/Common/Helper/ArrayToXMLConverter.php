<?php

namespace Common\Helper;

class ArrayToXMLConverter
{

    public static function array2xml($array, $wrap = 'ROW0', $upper = true)
    {
        // set initial value for XML string
        $xml = '';
        // wrap XML with $wrap TAG
        if ($wrap != null) {
            $xml .= "<$wrap>\n";
        }
        // main loop
        foreach ($array as $key => $value) {
            // set tags in uppercase if needed
            if ($upper == true) {
                $key = strtoupper($key);
            }
            // append to XML string
            $xml .= "<$key>" . htmlspecialchars(trim($value)) . "</$key>";
        }
        // close wrap TAG if needed
        if ($wrap != null) {
            $xml .= "\n</$wrap>\n";
        }
        // return prepared XML string
        return $xml;
    }
}
