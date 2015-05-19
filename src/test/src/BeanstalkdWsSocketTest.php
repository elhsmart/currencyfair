<?php

namespace CfTest;

use Pheanstalk\Pheanstalk;

require_once __DIR__ . '/../vendor/autoload.php';

$pheanstalk = new Pheanstalk("127.0.0.1", "11300");

function getRandomTestingJob() {
    $countries = array(
        "GB",
        "FR",
        "DE",
        "US",
        "UA",
        "RU",
        "CA",
        "JP",
        "CH",
        "JP"
    );

    $params = array_map(function () {
            return rand(100, 1000);
        }, range(0, 1));
    $params[] = round($params[0] / $params[1], 3);

    $testingJob = json_encode((object)array(
        "userId" => rand(1, 1000),
        "currencyFrom" => "EUR",
        "currencyTo" => "GBP",
        "amountSell" => $params[0],
        "amountBuy" => $params[1],
        "rate" => $params[2],
        "timePlaced" => "24-JAN-15 10:27:44",
        "originatingCountry" => $countries[array_rand($countries)]
    ));

    return $testingJob;
}

$pheanstalk->useTube("currencyfair_cfsocket")->put(getRandomTestingJob());

