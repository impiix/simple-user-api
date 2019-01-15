<?php

namespace Tests\Controller;

use App\Controller\DefaultController;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    protected static $userData;

    public function testRegister()
    {
        self::$userData = [
            'email' => 'test' . uniqid() . '@px.loc',
            'password' => 'test',
            'username' => 'test'. uniqid()
        ];

        $client = $this->createClient();
        $client->request(
            "POST",
            "/register",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(self::$userData)
            );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testLogin()
    {
        $client = $this->createClient();
        $client->request(
            "POST",
            "/login",
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(self::$userData)
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
