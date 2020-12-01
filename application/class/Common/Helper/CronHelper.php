<?php

namespace Common\Helper;

define('LOCK_DIR', './lock/');
define('LOCK_SUFFIX', '.lock');

class CronHelper
{

    private static $pid;

    function __construct()
    { }

    function __clone()
    { }

    private static function isrunning($lock_name = 'cronHelper')
    {
        $pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);

        // log_message("debug", "self pid = " . self::$pid . " | " . "pids = " . json_encode($pids));
        if (in_array(self::$pid, $pids)) {
            return TRUE;
        }
        return FALSE;
    }

    private static function isrunningCli($lock_name = 'cronHelper', $max_process = 6)
    {
        $pids = explode(PHP_EOL, `ps -e | grep " common Batch_cli $lock_name " | awk '{print $2}'`);
        log_message("debug", "cli pids count = " . count($pids));
        if (is_array($pids) && count($pids) >= $max_process) {
            return TRUE;
        }
        return FALSE;
    }

    private static function isrunningCurl($route = 'job/default', $max_process = 6)
    {
        $pids = explode(PHP_EOL, `ps -e | grep "$route" | grep -v grep`);
        log_message("debug", "curl pids count = " . count($pids));
        if (is_array($pids) && count($pids) > $max_process) {
            return TRUE;
        }
        return FALSE;
    }

    public static function lock($lock_name = 'cronHelper', $route = null, $lock_max_process = 6, $route_max_process = 6)
    {
        global $argv;

        if (defined('CROND_LOCK_DIR')) {
            define('LOCK_DIR', CROND_LOCK_DIR);
        }
        $lock_file = LOCK_DIR . $lock_name . LOCK_SUFFIX;

        if (!file_exists(LOCK_DIR)) {
            mkdir(LOCK_DIR, 0777, true);
        }

        if (!file_exists(LOCK_DIR)) {
            log_message("error", "==== LOCK_DIR not found ====");
            return FALSE;
        }

        if (!file_exists($lock_file)) {
            //not exists, not runing

            if (self::isrunningCli($lock_name, $lock_max_process)) {
                log_message("error", "== process cli mode == $lock_name Already in progress...");
                return FALSE;
            }

            if ($route != null) {
                if (self::isrunningCurl($route, $route_max_process)) {
                    log_message("error", "== process curl mode == $route Already in progress...");
                    return FALSE;
                }
            }

            self::$pid = getmypid();
            file_put_contents($lock_file, self::$pid);

            log_message("info", "== " . self::$pid . " == $lock_name Lock acquired, processing the job...");
            return self::$pid;
        }

        // Is running?
        self::$pid = file_get_contents($lock_file);
        if (self::isrunning($lock_name)) {
            log_message("error", "== " . self::$pid . " == $lock_name Already in progress...");
            return FALSE;
        } else if (self::isrunningCli($lock_name, $lock_max_process)) {
            log_message("error", "== process cli mode == $lock_name Already in progress...");
            return FALSE;
        } else if ($route != null && self::isrunningCurl($route, $route_max_process)) {
            log_message("error", "== process curl mode == $route Already in progress...");
            return FALSE;
        } else {
            log_message("error", "== " . self::$pid . " == $lock_name Previous job died abruptly...");
        }

        self::$pid = getmypid();
        file_put_contents($lock_file, self::$pid);
        return self::$pid;
    }

    public static function unlock($lock_name = 'cronHelper', $route = null)
    {
        global $argv;

        if (defined('CROND_LOCK_DIR')) {
            define('LOCK_DIR', CROND_LOCK_DIR);
        }
        $lock_file = LOCK_DIR . $lock_name . LOCK_SUFFIX;

        if (file_exists($lock_file))
            unlink($lock_file);

        log_message("info", "== " . self::$pid . " == Releasing lock...");
        return TRUE;
    }
}
