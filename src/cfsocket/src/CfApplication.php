<?php

namespace CfSocket;

use Pheanstalk\Pheanstalk;

require_once __DIR__ . '/../src/common.php';

class CfApplication {

    protected static $config;
    protected $clients = array();
    protected $beanstalk;

    public function __construct() {
        $this->config = AppCommon::getConfig();
        $this->beanstalk = new Pheanstalk($this->config['beanstalkd']['host'], $this->config['beanstalkd']['port']);
        $this->beanstalk->useTube($this->config['beanstalkd']['cfsocket_tube']);

    }

    public function onConnect($client) {
        $this->clients[$client->getId()] = $client;
    }

    public function onUpdate() {
        try {
            $job = $this->beanstalk->peekReady();
            var_dump($job);

            if ($job) {
                $data = $job->getData();

                if (strlen($data) > 0 && count($this->clients) > 0) {
                    foreach ($this->clients as $sendto) {
                        $sendto->send($data);
                    }
                }

                $this->beanstalk->delete($job);
            }

        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function onDisconnect($client) {
        $id = $client->getId();
        unset($this->clients[$id]);
    }

    public function onData() {
        return;
    }

}