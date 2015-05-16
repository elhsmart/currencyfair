<?php

namespace CfTest;

use Pheanstalk\Pheanstalk;

require_once __DIR__ . '/../vendor/autoload.php';

$pheanstalk = new Pheanstalk("127.0.0.1", "11300");

$testingJob = json_encode((object)array(
    "userId"                => "134256",
    "currencyFrom"          => "EUR",
    "currencyTo"            => "GBP",
    "amountSell"            => 1000,
    "amountBuy"             => 747.10,
    "rate"                  => 0.141,
    "timePlaced"            => "24-JAN-15 10:27:44",
    "originatingCountry"    => "FR"
));

$pheanstalk->useTube("cf")->put($testingJob);
