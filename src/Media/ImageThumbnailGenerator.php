<?php

namespace App\Media;

use Gumlet\ImageResize;
use Gumlet\ImageResizeException;
use InvalidArgumentException;
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
     * @param int|null $width
     * @param int|null $height
     * @return string
     * @throws ImageResizeException
     * @throws InvalidArgumentException
     */
    public function generate(string $filename, ?int $width = null, ?int $height = null): string
    {
        $sourceFilepath = $this->sourcePath.'/'.$filename;
        $thumbnailFilepath = $this->thumbnailPath.'/'.$filename;

        if (false === $this->filesystem->exists($sourceFilepath)) {
            throw new InvalidArgumentException(sprintf('Source file %s does not exist', $sourceFilepath));
        }

        if (false === $this->filesystem->exists(dirname($thumbnailFilepath))) {
            $this->filesystem->mkdir(dirname($thumbnailFilepath));
        }

        $width = $width ?? $this->defaultWidth;
        $height = $height ?? $this->defaultHeight;

        $maxShort = $width > $height ? $width : $height;

        $image = new ImageResize($sourceFilepath);
        $image->resizeToShortSide($maxShort, false);
        $image->crop($width, $height, false, ImageResize::CROPTOPCENTER);
        $image->save($thumbnailFilepath);

        return $thumbnailFilepath;
    }
}
