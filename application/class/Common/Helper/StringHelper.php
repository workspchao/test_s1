<?php

namespace Common\Helper;

class StringHelper
{
    /**
     * 去字段名中的下划线，每个单词首字母大写（如：acccess_token 转为 setAccountToken)
     * @param $type (get, set)
     * @param $field_name    字段名
     * @return null|string
     */
    public static function formatSetterGetter($type, $field_name) {
        $words = explode('_', $field_name);
        $newName = null;
        foreach ($words as $word) {
            $newName .= ucfirst($word);
        }
        return $type . $newName;
    }
    
}
