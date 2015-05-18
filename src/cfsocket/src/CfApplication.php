<?php

namespace CfSocket;

use Pheanstalk\Pheanstalk;

class CfApplication {

    protected $clients = array();
    protected $beanstalk;

    public function __construct(Pheanstalk $beanstalk) {
        $this->beanstalk = $beanstalk;
        $this->beanstalk->useTube("cf");
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