<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProfilePhotoTest extends WebTestCase
{
    public function testFileUpload()
    {
        $testFilePath = __DIR__.'/../../var/profile-photo.jpg';

        $fs = new Filesystem();
        $fs->copy(__DIR__.'/../fixtures/profile-photo.jpg', $testFilePath);

        /** @var Client $client */
        $client = static::createClient();
        $file = new UploadedFile(
            $testFilePath,
            'profile-photo.jpg',
            'image/jpeg',
            null
        );
        $client->request(
            'POST',
            '/api/profile-photos',
            [],
            ['file' => $file],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode(), $response->getContent());
        $this->assertJson($response->getContent());
        $data = json_decode($response->getContent());
        $this->assertNotEmpty($data->fileName);
        $this->assertEquals('ProfilePhoto', $data->{'@type'});

    }

    public function testInvalidFile()
    {
        $file = tmpfile();
        $testFilePath = stream_get_meta_data($file)['uri'];
        file_put_contents($testFilePath, 'test');

        /** @var Client $client */
        $client = static::createClient();
        $file = new UploadedFile(
            $testFilePath,
            basename($testFilePath),
            null,
            null
        );
        $client->request(
            'POST',
            '/api/profile-photos',
            [],
            ['file' => $file],
            [
                'HTTP_ACCEPT' => 'application/ld+json',
            ]
        );

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode(), $response->getContent());
    }
}