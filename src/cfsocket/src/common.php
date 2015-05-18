<?php

ini_set('include_path', ini_get('include_path').':..');

error_reporting(E_ALL);

System_Daemon::setOption("appName", "cfsocket");
System_Daemon::setOption("appExecutable", "cfsocket");
System_Daemon::setOption("appDescription", "Little daemon to handle CurrencyFair socket responses");
System_Daemon::setOption("authorName", "Ed Tretyakov");
System_Daemon::setOption("authorEmail", "elhsmart@gmail.com");

$config_file = "cfsocket.ini";

function is_json($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}