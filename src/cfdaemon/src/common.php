<?php

    error_reporting(E_ALL);

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