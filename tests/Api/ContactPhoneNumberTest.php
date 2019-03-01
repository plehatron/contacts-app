<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

class ContactPhoneNumberTest extends WebTestCase
{
    public function testPhoneNumberAssertion()
    {
        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/phone-numbers',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            <<<JSONLD
{
  "contact": {
    "id": 1
  },
  "number": "invalid"
}
JSONLD
        );
        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertJson($content);
        $data = json_decode($content);
        $this->assertEquals('ConstraintViolationList', $data->{'@type'});
        $this->assertEquals('number', $data->violations[0]->propertyPath);
    }
}
