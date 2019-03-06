<?php

namespace App\Tests\Media;

use App\Media\ImageThumbnailGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class ImageThumbnailGeneratorTest extends TestCase
{
    private $fs;
    private $thumbDirectory;
    private $sourcePath;
    private $fileName;
    private $filePath;

    public function setUp()
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->thumbDirectory = '/tmp/contacts-app-media';
        $this->sourcePath = __DIR__.'/../fixtures';
        $this->fileName = 'profile-photo.jpg';
        $this->filePath = $this->thumbDirectory.'/'.$this->fileName;

        if ($this->fs->exists($this->thumbDirectory)) {
            $this->fs->remove($this->thumbDirectory);
        }
    }

    public function tearDown()
    {
        parent::tearDown();

        if ($this->fs->exists($this->thumbDirectory)) {
            $this->fs->remove($this->thumbDirectory);
        }
    }

    public function testThumbGenerator()
    {

        $thumbGenerator = new ImageThumbnailGenerator($this->fs, $this->sourcePath, $this->thumbDirectory, 100, 100);
        $thumbGenerator->generate($this->fileName);

        $this->assertEquals($this->sourcePath, $thumbGenerator->getSourcePath());
        $this->assertEquals($this->thumbDirectory, $thumbGenerator->getThumbnailPath());
        $this->assertFileExists($this->filePath);
        $this->assertFileEquals(
            __DIR__.'/../fixtures/profile-photo-thumb.jpg',
            $this->filePath
        );
    }
}
