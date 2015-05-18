<?php

    ini_set('include_path', ini_get('include_path').':..');

    error_reporting(E_ALL);
    require_once "System/Daemon.php";

    System_Daemon::setOption("appName", "cfdaemon");
    System_Daemon::setOption("appExecutable", "cfdaemon");
    System_Daemon::setOption("appDescription", "Little daemon to handle CurrencyFair test app queue");
    System_Daemon::setOption("authorName", "Ed Tretyakov");
    System_Daemon::setOption("authorEmail", "elhsmart@gmail.com");

    $config_file = "cfdaemon.ini";

    function is_json($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }