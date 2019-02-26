<?php

namespace App\Tests\Api;

use App\Entity\Behaviours\Timestamps;
use App\Entity\Contact;
use App\Entity\ContactPhoneNumber;
use App\Repository\ContactRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group time-sensitive
 */
class ContactTest extends WebTestCase
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ContactRepository
     */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $kernel = self::bootKernel([]);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Contact::class);
    }

    private function assertJsonValidSchema(string $data, string $schema)
    {
        $data = json_decode($data);
        $validator = new \JsonSchema\Validator();
        $isNotValid = $validator->validate($data, json_decode($schema));
        $errors = $validator->getErrors();
        $errorMessage = array_reduce(
            $errors,
            function ($carry, $item) {
                return $carry."\n".sprintf(
                        "Property %s (%s) invalid: %s",
                        $item['property'],
                        $item['pointer'],
                        $item['message']
                    );
            }
        );
        $this->assertEquals(0, $isNotValid, $errorMessage);
    }

    public function testGetContacts()
    {
        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/contacts',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJsonValidSchema(
            $client->getResponse()->getContent(),
            file_get_contents(__DIR__.'/schemas/contact_list_schema.json')
        );
    }

    public function testCreateContact()
    {
        ClockMock::register(Timestamps::class);
        ClockMock::withClockMock(strtotime('2019-02-26T06:36:53+00:00'));

        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/contacts',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            <<<JSONLD
{
  "firstName": "John",
  "lastName": "Doe",
  "emailAddress": "john.doe@example.org",
  "favourite": false,
  "phoneNumbers": [
    {
      "number": "+3851234567",
      "label": "Home"
    }
  ]
}
JSONLD
        );
        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());

        $this->assertJsonValidSchema(
            $response->getContent(),
            file_get_contents(__DIR__.'/schemas/contact_schema.json')
        );
    }

    public function testValidationErrorWhenContactIsInvalid()
    {
        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'POST',
            '/api/contacts',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            <<<JSONLD
{
  "emailAddress": "notanemail",
  "favourite": false
}
JSONLD
        );
        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonStringEqualsJsonString(
            <<<'JSONLD'
{  
   "@context":"/api/contexts/ConstraintViolationList",
   "@type":"ConstraintViolationList",
   "hydra:title":"An error occurred",
   "hydra:description":"emailAddress: This value is not a valid email address.",
   "violations":[  
      {  
         "propertyPath":"emailAddress",
         "message":"This value is not a valid email address."
      }
   ]
}
JSONLD
            ,
            $response->getContent()
        );
    }

    public function testDeleteContact()
    {
        $contact = $this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']);

        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'DELETE',
            '/api/contacts/'.$contact->getId(),
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );
        $response = $client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $this->assertNull($this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']));
    }

    public function testSetFavourite()
    {
        $contact = $this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']);

        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/contacts/'.$contact->getId(),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/ld+json',
                'HTTP_ACCEPT' => 'application/ld+json',
            ],
            <<<JSONLD
{
  "favourite": false
}
JSONLD
        );
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertTrue($contact->getFavourite());
        $contact = $this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']);
        $this->assertFalse($contact->getFavourite());
    }
}