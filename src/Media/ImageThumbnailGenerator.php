<?php

namespace App\Media;

use Gumlet\ImageResize;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Image thumbnail generator creates a thumbnail from the source directory and stores it in the thumbnail directory path.
 *
 * @package App\Media
 */
class ImageThumbnailGenerator
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $sourcePath;

    /**
     * @var string
     */
    private $thumbnailPath;

    /**
     * @var int
     */
    private $defaultWidth;
    /**
     * @var int
     */
    private $defaultHeight;

    public function __construct(
        Filesystem $filesystem,
        string $sourcePath,
        string $thumbnailPath,
        int $width,
        int $height
    ) {
        $this->filesystem = $filesystem;
        $this->sourcePath = $sourcePath;
        $this->thumbnailPath = $thumbnailPath;
        $this->defaultWidth = $width;
        $this->defaultHeight = $height;
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
    public function generate(string $filename, ?int $width = null, ?int $height = null): string
    {
        $sourceFilepath = $this->sourcePath.'/'.$filename;
        $thumbnailFilepath = $this->thumbnailPath.'/'.$filename;

        if (false === $this->filesystem->exists(dirname($thumbnailFilepath))) {
            $this->filesystem->mkdir(dirname($thumbnailFilepath));
        }

        $maxShort = $this->defaultWidth > $this->defaultHeight ? $this->defaultWidth : $this->defaultHeight;

        $image = new ImageResize($sourceFilepath);
        $image->resizeToShortSide($maxShort, false);
        $image->crop($this->defaultWidth, $this->defaultHeight, false, ImageResize::CROPTOPCENTER);
        $image->save($thumbnailFilepath);

        return $thumbnailFilepath;
    }
}
