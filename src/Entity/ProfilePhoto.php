<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\CreateProfilePhotoAction;
use App\Entity\Behaviours\Timestamps;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ApiResource(
 *     collectionOperations={
 *         "get",
 *         "post"={
 *             "method"="POST",
 *             "path"="/profile-photos",
 *             "controller"=CreateProfilePhotoAction::class,
 *             "defaults"={"_api_receive"=false},
 *         },
 *     },
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"="true"},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"="true"},
 * )
 * @ORM\Entity
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 */
class ProfilePhoto
{
    use Timestamps;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="profile_photo", fileNameProperty="fileName")
     * @Assert\File(
     *     maxSize = "5M",
     *     mimeTypes = {"image/png", "image/jpeg"}
     * )
     */
    private $file;

    /**
     * @var string|null
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    private $fileName;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return File|null
     */
    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param File|null $file
     * @return ProfilePhoto
     * @throws \Exception
     */
    public function setFile(?File $file): self
    {
        $this->file = $file;
        if ($this->file) {
            $this->updatedAt = new DateTime('now');
        }

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string|null $fileName
     * @return ProfilePhoto
     */
    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }


}