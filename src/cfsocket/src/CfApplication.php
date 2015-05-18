<?php

namespace CfSocket;

use Pheanstalk\Pheanstalk;

require_once __DIR__ . '/../src/common.php';

class CfApplication {

    protected static $config;
    protected $clients = array();
    protected $beanstalk;

    public function __construct() {
        $this->config = $this->getConfig();
        $this->beanstalk = new Pheanstalk($this->config['beanstalkd']['host'], $this->config['beanstalkd']['port']);
        $this->beanstalk->useTube($this->config['beanstalkd']['cfsocket_tube']);
    }

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

    public function onConnect($client) {
        $this->clients[$client->getId()] = $client;
    }

    public function onUpdate() {
        try {
            $job = $this->beanstalk->peekReady();

            if ($job) {
                $data = $job->getData();

                if (strlen($data) > 0 && count($this->clients) > 0) {
                    foreach ($this->clients as $sendto) {
                        $sendto->send($data);
                    }
                }

                $this->beanstalk->delete($job);
            }

        } catch (\Exception $e) {}
    }

    public function onDisconnect($client) {
        $id = $client->getId();
        unset($this->clients[$id]);
    }

    public function onData() {
        return;
    }

}