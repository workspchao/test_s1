<?php

namespace Common\Helper;

class ArrayExtractor
{

    public static function extract(array $from_array, array $fields)
    {
        $new_array = array();
        $i = 0;
        foreach ($from_array as $key => $value) {
            if (array_search($key, $fields) !== FALSE || array_key_exists($key, $fields) !== FALSE) {
                $new_fields = NULL;
                if (isset($fields[$i]) && is_array($fields[$i])) {
                    $new_fields = $fields[$i];
                } else if (isset($fields[$key]) && is_array($fields[$key])) {
                    $new_fields = $fields[$key];
                }

                if (is_array($value) && is_array($new_fields)) {
                    $new_array[$key] = self::extract($value, $new_fields);
                } else {
                    $new_array[$key] = $value;
                }

                $i++;
            }
        }

        return $new_array;
    }
}
