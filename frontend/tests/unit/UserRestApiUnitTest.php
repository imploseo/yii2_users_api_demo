<?php

namespace frontend\tests;

use yii\helpers\JSON;
use \common\models\User;
use \common\models\UserStatusInactive;
use \frontend\modules\api_v1\controllers\UserController;

class UserRestApiUnitTest extends \Codeception\Test\Unit
{
    const REST_API_SITE_URL = 'http://yii2-users-api.my';

    /**
     * @var \frontend\tests\UnitTester
     */
    protected $tester;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $http;
    protected $testUserData = [
        'name' => 'TestUser123',
        'status' => UserStatusInactive::CODE,
    ];

    public function setUp()
    {
        $this->http = new \GuzzleHttp\Client([
            'base_uri' => self::REST_API_SITE_URL,
            'headers' => ['X-Auth-Token' => UserController::ACCESS_TOKEN]
        ]);
    }

    public function tearDown()
    {
        $this->http = null;
    }

    protected function _before()
    {
        $this->setUp();
    }

    protected function _after()
    {
        $this->tearDown();
    }

    public function testGet()
    {
        $response = $this->http->get('/api_v1/users');
        $this->assertEquals(200, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=UTF-8", $contentType);
        $body = $response->getBody();
        $users = JSON::decode($body);
        $this->assertNotEmpty($users);
    }

    public function testCreate()
    {
        $response = $this->http->post('/api_v1/users', ['form_params' => $this->testUserData]);
        $this->assertEquals(201, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=UTF-8", $contentType);
        $body = $response->getBody();
        $user = JSON::decode($body);
        $this->assertNotEmpty($user);
        $this->assertEquals($user['name'], $this->testUserData['name']);
        $this->assertEquals($user['status'], $this->testUserData['status']);
    }

    public function testGetOne()
    {
        $response = $this->http->post('/api_v1/users', ['form_params' => $this->testUserData]);
        $body = $response->getBody();
        $user = JSON::decode($body);
        $response = $this->http->get('/api_v1/users/' . $user['id']);
        $this->assertEquals(200, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=UTF-8", $contentType);
        $body = $response->getBody();
        $user = JSON::decode($body);
        $this->assertNotEmpty($user);
        $this->assertEquals($user['name'], $this->testUserData['name']);
        $this->assertEquals($user['status'], $this->testUserData['status']);
    }

    public function testUpdate()
    {
        $response = $this->http->post('/api_v1/users', ['form_params' => $this->testUserData]);
        $body = $response->getBody();
        $user = JSON::decode($body);
        $newTestName = 'NewTestName';
        $response = $this->http->put('/api_v1/users/' . $user['id'],
                                    ['form_params' => ['name' => $newTestName]]);
        $this->assertEquals(200, $response->getStatusCode());
        $contentType = $response->getHeaders()["Content-Type"][0];
        $this->assertEquals("application/json; charset=UTF-8", $contentType);
        $body = $response->getBody();
        $user = JSON::decode($body);
        $this->assertNotEmpty($user);
        $this->assertEquals($user['name'], $newTestName);
        $this->assertEquals($user['status'], $this->testUserData['status']);
    }

    public function testDelete()
    {
        $response = $this->http->post('/api_v1/users', ['form_params' => $this->testUserData]);
        $body = $response->getBody();
        $user = JSON::decode($body);
        $response = $this->http->delete('/api_v1/users/' . $user['id']);
        $this->assertEquals(204, $response->getStatusCode());
    }
}