<?php
namespace ApiTests;

use Guzzle\Guzzle;

error_reporting( -1 );

require_once __DIR__ . '/../vendor/autoload.php';

class ApiEndpointTest extends \PHPUnit_Framework_TestCase {

    public static $config;
    public $client;

    public function setUp() {
        self::$config = $this->getConfig();
        $this->client = new \Guzzle\Service\Client('http://' . self::$config['web']['domain'], array(
            'request.options' => array(
                'exceptions' => false,
            )
        ));
    }

    public function testRequestPassed() {
        $testData = $this->getTestData();
        $request = $this->client->post('/trade', array(), json_encode($testData));
        $response = $request->send();

        $this->assertEquals(200, $response->getStatusCode());
        self::isJson()->evaluate($response->getBody(true));
        $data = json_decode($response->getBody(true));
        $this->assertObjectHasAttribute("status", $data);
        $this->assertEquals($data->status, "success");
    }

    public function testEmptyRequest() {
        try {
            $request = $this->client->post('/trade', array(), " ");
            $response = $request->send();
        } catch (\Exception $e) {
            $response = $e->getResponse();
        }

        $this->assertEquals(400, $response->getStatusCode());
        self::isJson()->evaluate($response->getBody(true));
        $data = json_decode($response->getBody(true));
        $this->assertObjectHasAttribute("status", $data);
        $this->assertEquals($data->status, "error");
    }

    public function testParamsUnexist() {
        $params = array(
            "userId",
            "currencyFrom",
            "currencyTo",
            "amountSell",
            "amountBuy",
            "rate",
            "timePlaced",
            "originatingCountry"
        );

        foreach($params as $param) {
            $testData = $this->getTestData();
            unset($testData->{$param});

            try {
                $request = $this->client->post('/trade', array(), json_encode($testData));
                $response = $request->send();
            } catch (\Exception $e) {
                $response = $e->getResponse();
            }

            $this->assertEquals(400, $response->getStatusCode());
            self::isJson()->evaluate($response->getBody(true));
            $data = json_decode($response->getBody(true));
            $this->assertObjectHasAttribute("status", $data);
            $this->assertEquals($data->status, "error");
            $this->assertObjectHasAttribute("params", $data);
            $this->assertObjectHasAttribute($param, $data->params);
        }
    }

    public function testParamsFail() {
        $params = array(
            "userId",
            "currencyFrom",
            "currencyTo",
            "amountSell",
            "amountBuy",
            "rate",
            "timePlaced",
            "originatingCountry"
        );

        foreach($params as $param) {
            $testData = $this->getTestData();
            $testData->{$param} = "nya";

            try {
                $request = $this->client->post('/trade', array(), json_encode($testData));
                $response = $request->send();
            } catch (\Exception $e) {
                $response = $e->getResponse();
            }

            $this->assertEquals(400, $response->getStatusCode());
            self::isJson()->evaluate($response->getBody(true));
            $data = json_decode($response->getBody(true));
            $this->assertObjectHasAttribute("status", $data);
            $this->assertEquals($data->status, "error");
            $this->assertObjectHasAttribute("params", $data);
            $this->assertObjectHasAttribute($param, $data->params);
        }
    }


    public function getConfig() {

        $config_error = false;
        if(is_array(self::$config)) {
            return self::$config;
        }

        $config_file = "cftest.ini";
        if(!is_file(__DIR__ . '/../conf/' . $config_file)) {
            $config_error = true;
        }

        $config = parse_ini_file(__DIR__ . '/../conf/' . $config_file, true);

        if(!$config) {
            $config_error = true;
        }

        if($config_error) {
            $this->fail("Config file can't be loaded");
        }

        return $config;
    }

    public function getTestData() {
        return (object)array(
            "userId"=> "134256",
            "currencyFrom"=> "EUR",
            "currencyTo"=> "GBP",
            "amountSell"=> 1000,
            "amountBuy"=> 747.10,
            "rate"=> 0.141,
            "timePlaced" => "24-JAN-15 10:27:44",
            "originatingCountry" => "FR"
        );
    }
}