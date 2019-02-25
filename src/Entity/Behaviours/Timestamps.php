<?php

namespace App\Entity\Behaviours;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

trait Timestamps
{
    /**
     * @ORM\Column(name="created_at", type="datetime")
     * @Groups({"read"})
     */
    private $createdAt;

    /**
     * @ORM\Column(name="updated_at", type="datetime")
     * @Groups({"read"})
     */
    private $updatedAt;

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function updateTimestamps()
    {
        // Create a datetime with microseconds
        $dateTime = new DateTimeImmutable('now');
        if (null === $this->createdAt) {
            $this->createdAt = $dateTime;
        }
        $this->updatedAt = $dateTime;
    }
}