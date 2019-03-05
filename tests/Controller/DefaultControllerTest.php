<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'GET',
            '/',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'text/html',
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
