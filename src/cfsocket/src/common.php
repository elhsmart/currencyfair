<?php

namespace CfSocket;

ini_set('include_path', ini_get('include_path').':..');

error_reporting(E_ALL);

\System_Daemon::setOption("appName", "cfsocket");
\System_Daemon::setOption("appExecutable", "cfsocket");
\System_Daemon::setOption("appDescription", "Little daemon to handle CurrencyFair socket responses");
\System_Daemon::setOption("authorName", "Ed Tretyakov");
\System_Daemon::setOption("authorEmail", "elhsmart@gmail.com");

$config_file = "cfsocket.ini";

function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

class AppCommon {

    public static $config;

    public static function getConfig() {

        $config_error = false;
        if(is_array(self::$config)) {
            return self::$config;
        }

        $config_file = "cfsocket.ini";
        if(!is_file(__DIR__ . '/../conf/' . $config_file)) {
            $config_error = true;
            \System_Daemon::log(\System_Daemon::LOG_CRIT, "Cannot load config file.");
        }

        $config = parse_ini_file(__DIR__ . '/../conf/' . $config_file, true);

        if(!$config) {
            $config_error = true;
            \System_Daemon::log(\System_Daemon::LOG_CRIT, "Cannot parse config file.");
        }

        if($config_error) {
            exit(1);
        }

        self::$config = $config;
        return self::$config;
    }
}