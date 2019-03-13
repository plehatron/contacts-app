<?php

namespace App\Tests\Api;

use App\Entity\Behaviours\Timestamps;
use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
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
     * @var Registry
     */
    private $doctrine;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var ContactRepository
     */
    private $repository;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $defaultClientServerHeaders;

    protected function setUp()
    {
        parent::setUp();

        $kernel = self::bootKernel([]);

        $this->doctrine = $kernel->getContainer()->get('doctrine');
        $this->connection = $this->doctrine->getConnection();
        $this->entityManager = $this->doctrine->getManager();
        $this->repository = $this->entityManager->getRepository(Contact::class);

        $this->client = $kernel->getContainer()->get('test.client');
        $this->client->disableReboot();
        $this->defaultClientServerHeaders = [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/ld+json',
        ];
    }

    public function testGetContacts()
    {
        $this->client->request(
            'GET',
            '/api/contacts',
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertJsonValidSchema(
            $this->client->getResponse()->getContent(),
            file_get_contents(__DIR__.'/schemas/contact_list_schema.json')
        );
    }

    public function testCreateAndDeleteContact()
    {
        ClockMock::register(Timestamps::class);
        ClockMock::withClockMock(strtotime('2019-02-26T06:36:53+00:00'));

        // Test create
        $this->client->request(
            'POST',
            '/api/contacts',
            [],
            [],
            $this->defaultClientServerHeaders,
            <<<JSONLD
{
  "firstName": "John",
  "lastName": "Walker",
  "emailAddress": "john.walker@example.org",
  "favourite": false,
  "phoneNumbers": [
    {
      "number": "+385918765432",
      "label": "Home"
    }
  ]
}
JSONLD
        );
        $response = $this->client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertJsonValidSchema(
            $response->getContent(),
            file_get_contents(__DIR__.'/schemas/contact_schema.json')
        );

        // Test delete
        $contact = $this->repository->findOneBy(['emailAddress' => 'john.walker@example.org']);
        $this->client->request(
            'DELETE',
            '/api/contacts/'.$contact->getId(),
            [],
            [],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );
        $response = $this->client->getResponse();
        $this->assertEquals(204, $response->getStatusCode());
        $contact = $this->repository->findOneBy(['emailAddress' => 'john.walker@example.org']);
        $this->assertTrue(null === $contact);
    }

    public function testValidationErrorWhenContactIsInvalid()
    {
        $this->client->request(
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
        $response = $this->client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent());
        $this->assertEquals('emailAddress: This value is not a valid email address.', $data->{'hydra:description'});
    }

    public function testSetFavourite()
    {
        $contact = $this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']);
        $this->assertTrue($contact->getFavourite());

        $this->client->request(
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
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode(), $response->getContent());
        $this->assertJson($response->getContent());
        $this->assertFalse($contact->getFavourite());
    }

    public function testSavePhoneNumbers()
    {
        $contact = $this->repository->findOneBy(['emailAddress' => 'joan.doe@example.org']);

        $this->client->request(
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
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent());
        $this->assertCount(2, $data->phoneNumbers);
        $this->assertEquals('+385912345678', $data->phoneNumbers[0]->number);
        $this->assertEquals('+385918765432', $data->phoneNumbers[1]->number);
    }

    public function testSearch()
    {
        $this->client->request(
            'GET',
            '/api/contacts?query=Joan',
            [],
            [],
            $this->defaultClientServerHeaders
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent());
        $this->assertGreaterThan(0, $content->{'hydra:totalItems'});
    }

    public function testSearchTermWithSpaces()
    {
        $this->client->request(
            'GET',
            '/api/contacts?query=joan%20doe',
            [],
            [],
            $this->defaultClientServerHeaders
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent());
        $this->assertGreaterThan(0, $content->{'hydra:totalItems'});
    }

    public function testSearchWithPhoneNumber()
    {
        $this->client->request(
            'GET',
            '/api/contacts?query=+385912345678',
            [],
            [],
            $this->defaultClientServerHeaders
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent());
        $this->assertGreaterThan(0, $content->{'hydra:totalItems'});
    }

    public function testSearchWithNoResults()
    {
        $this->client->request(
            'GET',
            '/api/contacts?query=nonexistentkeyword',
            [],
            [],
            $this->defaultClientServerHeaders
        );
        $response = $this->client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $content = json_decode($response->getContent());
        $this->assertEquals(0, $content->{'hydra:totalItems'});
    }
}