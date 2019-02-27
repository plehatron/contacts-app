<?php

namespace App\Media;

use Gumlet\ImageResize;

class ImageThumbnailGenerator
{
    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $thumbnailPath;

    public function __construct(string $sourcePath, string $thumbnailPath)
    {
        $this->sourcePath = $sourcePath;
        $this->thumbnailPath = $thumbnailPath;
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function getThumbnailPath(): string
    {
        return $this->thumbnailPath;
    }

    /**
     * @param string $filename
     * @param int $width
     * @param int $height
     * @return string
     * @throws \Gumlet\ImageResizeException
     */
    public function generate(string $filename, int $width, int $height): string
    {
        $sourceFilepath = $this->sourcePath.'/'.$filename;
        $thumbnailFilepath = $this->thumbnailPath.'/'.$filename;

        $image = new ImageResize($sourceFilepath);
        $image->resize($width, $height, false);
        $image->save($thumbnailFilepath);

        return $thumbnailFilepath;
    }
}
