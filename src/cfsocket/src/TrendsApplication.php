<?php

namespace CfSocket;

use Pheanstalk\Pheanstalk;

require_once __DIR__ . '/../src/common.php';

class TrendsApplication {
    protected static $config;
    protected $clients = array();

    public function __construct() {
        $this->config = AppCommon::getConfig();
    }

    public function onConnect($client) {
        $this->clients[$client->getId()] = $client;
    }

    public function onDisconnect($client) {
        $id = $client->getId();
        unset($this->clients[$id]);
    }

    public function onData($payload, $client) {
        if(!is_json($payload)) {
            $client->send(json_encode(array("status" => "error", "error" => "Non-JSON provided")));
            return;
        }
        $data = json_decode($payload);

        $MC = new \Memcache;
        $MC->connect($this->config['memcache']['host'], $this->config['memcache']['port']);

        $country = $MC->get($data->country);

        if(empty($country)) {
            $client->send(json_encode(array("status" => "error", "error" => "Country have no any data.")));
            return;
        }
        $country = json_decode($country);
        $country->code = $data->country;

        $client->send(json_encode(array("status" => "success", "countryData" => $country)));
    }
}