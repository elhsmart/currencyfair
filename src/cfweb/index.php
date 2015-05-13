<?php

require 'vendor/autoload.php';

class ApiResponseHandler {

    public static function echoResponse($status_code, $response) {
        $app = \Slim\Slim::getInstance();
        // Http response code
        $app->status($status_code);

        // setting response content type to json
        $app->contentType('application/json');

        echo json_encode($response);
    }
}

class EndpointParamsHandler {

    private $lastError = false;

    private $currenciesAvailable = array(
        "GBP",
        "EUR",
        "USD",
        "UAH",
        "RUR",
        "CAD",
        "JPY",
        "CHF"
    );

    private $countriesAvailable = array(
        "GB",
        "FR",
        "DE",
        "US",
        "UA",
        "RU",
        "CA",
        "JP",
        "CH"
    );

    public function __construct() {}

    public function getLastError() {
        $error = $this->lastError;
        $this->lastError = false;
        return $error;
    }

    public function validateUserId($id) {
        if((int)$id > 0) {
            return true;
        }
        $this->lastError = "User Id is not valid int";
        return false;
    }

    private function validateCurrency($currencyName) {
        if(in_array($currencyName, $this->currenciesAvailable)) {
            return true;
        }
        $this->lastError = "Currency provided not in valid currencies list. Must be one of: " . implode(", ", $this->currrenciesAvailable);
        return false;
    }

    public function validateCurrencyTo($currencyName) {
        return $this->validateCurrency($currencyName);
    }

    public function validateCurrencyFrom($currencyName) {
        return $this->validateCurrency($currencyName);
    }

    private function validateCountry($countryName) {
        if(in_array($countryName, $this->countriesAvailable)) {
            return true;
        }
        $this->lastError = "Country provided not in valid countries list. Must be one of: " . implode(", ", $this->countriesAvailable);
        return false;
    }

    public function validateOriginatingCountry($countryName) {
        return $this->validateCountry($countryName);
    }

    private function validateAmount($amount) {
        if((float)$amount > 0) {
            return true;
        }
        $this->lastError = "Amount is not valid float";
        return false;
    }

    public function validateAmountBuy($amount) {
        return $this->validateAmount($amount);
    }

    public function validateAmountSell($amount) {
        return $this->validateAmount($amount);
    }

    public function validateRate($amount) {
        if($this->validateAmount($amount)) {
            return true;
        }
        $this->lastError = "Rate is not valid float";
        return false;
    }

    public function validateTimePlaced($timePlaced) {
        if (DateTime::createFromFormat('d-M-y H:i:s', $timePlaced) !== FALSE) {
            return true;
        }
        $this->lastError = "Time placed format is wrong. Must be DD-MMM-YY HH:MM:SS";
        return false;
    }

    public function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}

$app = new \Slim\Slim(array(
    'debug' => true
));

$app->get('/', function () {
    echo "Hello! This is Slim!";
});

$app->post('/trade', function () {
    $app = \Slim\Slim::getInstance();
    $validator = new EndpointParamsHandler();

    $checkParameters = array(
        "userId",
        "currencyFrom",
        "currencyTo",
        "amountSell",
        "amountBuy",
        "rate",
        "timePlaced",
        "originatingCountry"
    );

    $requestBody = $app->request->getBody();

    if(strlen($requestBody) == 0 || !$validator->isJson($requestBody)) {
        ApiResponseHandler::echoResponse(400, array(
            "error" => "JSON must be provided"
        ));
        $app->stop();
    }

    $errorFields = array();
    $requestFields = (array)json_decode($requestBody);

    foreach($checkParameters as $key => $param) {
        if(!array_key_exists($param, $requestFields)) {
            $errorFields[$param] = "This field is required";
            continue;
        }
        if(!$validator->{"validate" . ucfirst($param)}($requestFields[$param])) {
            $errorFields[$param] = $validator->getLastError();
        }
    }

    if(count($errorFields) > 0) {
        ApiResponseHandler::echoResponse(400, array(
            "error" => "Wrong parameters provided",
            "params" => $errorFields
        ));
        $app->stop();
    }


});

$app->run();