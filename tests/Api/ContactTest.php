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
    use AssertJsonValidSchema;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ContactRepository
     */
    private $repository;

    /**
     * @var array
     */
    private $defaultClientServerHeaders;

    protected function setUp()
    {
        parent::setUp();

        $kernel = self::bootKernel([]);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(Contact::class);

        $this->defaultClientServerHeaders = [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json',
        ];
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
            $this->defaultClientServerHeaders,
            <<<JSONLD
{
  "firstName": "John",
  "lastName": "Doe",
  "emailAddress": "john.doe@example.org",
  "favourite": false,
  "phoneNumbers": [
    {
      "number": "+385912345678",
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
            $this->defaultClientServerHeaders,
            <<<JSONLD
{
  "firstName": "John",
  "lastName": "Doe",
  "emailAddress": "notanemail",
  "favourite": false
}
JSONLD
        );
        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent());
        $this->assertEquals('emailAddress: This value is not a valid email address.', $data->{'hydra:description'});
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
            $this->defaultClientServerHeaders,
            <<<JSONLD
{
  "favourite": false
}
JSONLD
        );
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertJson($response->getContent());
        $this->assertTrue($contact->getFavourite());
        $contact = $this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']);
        $this->assertFalse($contact->getFavourite());
    }

    public function testSavePhoneNumbers()
    {
        $contact = $this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']);

        /** @var Client $client */
        $client = static::createClient();
        $client->request(
            'PUT',
            '/api/contacts/'.$contact->getId(),
            [],
            [],
            $this->defaultClientServerHeaders,
            <<<JSONLD
{
  "phoneNumbers": [
    {"number":"+385912345678","label":"Mobile"},
    {"number":"+385918765432","label":"Home"}
  ]
}
JSONLD
        );
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent());
        $this->assertCount(2, $data->phoneNumbers);
        $this->assertEquals('+385912345678', $data->phoneNumbers[0]->number);
        $this->assertEquals('+385918765432', $data->phoneNumbers[1]->number);
    }

    public function testSearch()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/contacts?query=Joan',
            [],
            [],
            $this->defaultClientServerHeaders
        );
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent());
        $this->assertEquals(1, $content->{'hydra:totalItems'});
    }

    public function testSearchTermWithSpaces()
    {
        $client = static::createClient();
        $client->request(
            'GET',
            '/api/contacts?query=joan%20doe',
            [],
            [],
            $this->defaultClientServerHeaders
        );
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent());
        $this->assertEquals(1, $content->{'hydra:totalItems'});
    }
}