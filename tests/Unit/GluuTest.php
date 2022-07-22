<?php

namespace Tests\Unit;

use Gluu\App\Controller\GluuController;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class GluuTest extends TestCase
{
    private const ACCESS_TOKEN = 123;

    public function setUpHttpMock(int $http_code, string $response): \GuzzleHttp\Client
    {
        $handler = new MockHandler([
            new Response($http_code, [], $response),
        ]);
        $httpMock = HandlerStack::create($handler);
        return new \GuzzleHttp\Client([
            'verify' => false,
            'base_uri' => env('gluu_base_url'),
            'auth' => [env('gluu_client_id'),env('gluu_client_secret')],
            'handler' => $httpMock,
            'http_errors' => false
        ]);
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAuthenticateSuccess()
    {
        $username = "admin";
        $password = "123456";
        $grantType = "";
        $scope =  "" ;

        $gluu = new GluuController();
        $gluuHttpClient = $this->setUpHttpMock(200, '{"access_token" : '.self::ACCESS_TOKEN.'}');
        $gluu->setGluuHttpClient($gluuHttpClient);
        $result = $gluu->authenticate($username, $password, $grantType, $scope);
        $this->assertEquals($result, self::ACCESS_TOKEN);
    }

    public function testAuthenticateFail()
    {
        $username = "admin";
        $password = "123456";
        $grantType = "";
        $scope =  "" ;
        $gluu = new GluuController();
        $gluuHttpClient = $this->setUpHttpMock(401, '{"error":"authenticate fail"}');
        $gluu->setGluuHttpClient($gluuHttpClient);
        $result = $gluu->authenticate($username, $password, $grantType, $scope);
        $this->assertNull($result);
    }
}
