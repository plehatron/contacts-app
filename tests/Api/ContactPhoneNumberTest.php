<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;

class ContactPhoneNumberTest extends WebTestCase
{
    use AssertJsonValidSchema;

    public function testGet()
    {
        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/phone-numbers',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJsonValidSchema(
            $client->getResponse()->getContent(),
            file_get_contents(__DIR__.'/schemas/contact_phone_number_schema.json')
        );
    }

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
