<?php

namespace Common\Helper;

class RandomHelper {

    /**
     * [{"key":"k1","name":"配置1","num":50},{"key":"k2","name":"配置2","num":50},{"key":"k3","name":"配置3","num":50}]
     * @param type $config_json
     * @return type
     */
    public static function getRandomNumByKey($config_json) {

        $random_key = null;
        $config = json_decode($config_json);

        $total_num = 0;
        foreach ($config as $key => $value) {
            //echo $value->key . PHP_EOL;
            $total_num = $total_num + $value->num;
        }

        $all_nums = array();
        foreach ($config as $key => $value) {
            $value->nums = array();
            for ($i = 0; $i < $value->num; $i++) {
                $num = random_int(1, $total_num);
                if (!in_array($num, $all_nums)) {
                    $value->nums[] = $num;
                    $all_nums[] = $num;
                    //echo "all_nums = " . json_encode($all_nums) . PHP_EOL;
                } else {
                    $i--;
                }
            }
            //echo $value->key . "|" . $value->num . " nums = " . json_encode($value->nums) . PHP_EOL;
        }

        $num = random_int(1, $total_num);
        foreach ($config as $key => $value) {
            if (in_array($num, $value->nums)) {
                $random_key = $value->key;
                break;
            }
        }

        return $random_key;
    }

    
    /**
     * [{"level":"k1","name":"配置1","num":50},{"level":"k2","name":"配置2","num":50},{"level":"k3","name":"配置3","num":50}]
     * @param type $config_json
     * @return type
     */
    public static function getRandomNumByLevel($config_json) {

        $random_key = null;
        $config = json_decode($config_json);

        $total_num = 0;
        foreach ($config as $key => $value) {
            //echo $value->key . PHP_EOL;
            $total_num = $total_num + $value->num;
        }

        $all_nums = array();
        foreach ($config as $key => $value) {
            $value->nums = array();
            for ($i = 0; $i < $value->num; $i++) {
                $num = random_int(1, $total_num);
                if (!in_array($num, $all_nums)) {
                    $value->nums[] = $num;
                    $all_nums[] = $num;
                    //echo "all_nums = " . json_encode($all_nums) . PHP_EOL;
                } else {
                    $i--;
                }
            }
            //echo $value->key . "|" . $value->num . " nums = " . json_encode($value->nums) . PHP_EOL;
        }

        $num = random_int(1, $total_num);
        foreach ($config as $key => $value) {
            if (in_array($num, $value->nums)) {
                $random_key = $value->level;
                break;
            }
        }

        return $random_key;
    }

    /**
     * [{"$key_name":"k1","name":"配置1","num":50},{"$key_name":"k2","name":"配置2","num":50},{"$key_name":"k3","name":"配置3","num":50}]
     * @param type $config_json
     * @return type
     */
    public static function getRandomNumByKeyName($config_json, $key_name = 'key') {

        log_message("debug", "getRandomNumByKeyName - $key_name - $config_json");
        
        $random_key = null;
        $config = json_decode($config_json);

        $total_num = 0;
        foreach ($config as $key => $value) {
            //echo $value->key . PHP_EOL;
            $total_num = $total_num + $value->num;
        }

        $all_nums = array();
        foreach ($config as $key => $value) {
            $value->nums = array();
            for ($i = 0; $i < $value->num; $i++) {
                $num = random_int(1, $total_num);
                if (!in_array($num, $all_nums)) {
                    $value->nums[] = $num;
                    $all_nums[] = $num;
                    //echo "all_nums = " . json_encode($all_nums) . PHP_EOL;
                } else {
                    $i--;
                }
            }
            //echo $value->key . "|" . $value->num . " nums = " . json_encode($value->nums) . PHP_EOL;
        }

        $num = random_int(1, $total_num);
        foreach ($config as $key => $value) {
            if (in_array($num, $value->nums)) {
                $random_key = $value->$key_name;
                break;
            }
        }

        return $random_key;
    }

}
