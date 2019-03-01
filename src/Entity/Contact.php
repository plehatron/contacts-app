<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use App\Entity\Behaviours\Timestamps;
use App\Filter\ContactSearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}, "enable_max_depth"="true"},
 *     denormalizationContext={"groups"={"write"}, "enable_max_depth"="true"},
 *     attributes={"order"={"firstName": "ASC"}}
 * )
 * @ApiFilter(ContactSearchFilter::class)
 * @ApiFilter(BooleanFilter::class, properties={"favourite"})
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @ORM\Table(name="contact", indexes={
 *     @ORM\Index(columns={"first_name", "last_name", "email_address"}, flags={"fulltext"})})
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
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
     * @Assert\Length(max=255)
     * @ORM\Column(name="first_name", type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     */
    private $firstName;

    /**
     * @Assert\Length(max=255)
     * @ORM\Column(name="last_name", type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     */
    private $lastName;

    /**
     * @Assert\Email
     * @Assert\Length(max=255)
     * @ORM\Column(name="email_address", type="string", length=255, nullable=true)
     * @Groups({"read", "write"})
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="profile_photo", type="string", length=255, nullable=true)
     * @Groups({"read"})
     */
    private $profilePhoto;

    /**
     * @ORM\Column(name="favourite", type="boolean")
     * @Groups({"read", "write"})
     */
    private $favourite = false;

    /**
     * @var ContactPhoneNumber[]
     *
     * @Assert\Valid()
     * @ORM\OneToMany(targetEntity="App\Entity\ContactPhoneNumber", mappedBy="contact", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Groups({"read", "write"})
     * @MaxDepth(1)
     */
    private $phoneNumbers;

    public function __construct()
    {
        $this->phoneNumbers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(?string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;

        return $this;
    }

    public function getFavourite(): ?bool
    {
        return $this->favourite;
    }

    public function setFavourite(bool $favourite): self
    {
        $this->favourite = $favourite;

        return $this;
    }

    /**
     * @return Collection|ContactPhoneNumber[]
     */
    public function getPhoneNumbers(): iterable
    {
        return $this->phoneNumbers;
    }

    public function setPhoneNumbers($phoneNumbers): self
    {
        $this->phoneNumbers = new ArrayCollection($phoneNumbers);

        return $this;
    }

    public function addPhoneNumber(ContactPhoneNumber $phoneNumber): self
    {
        if (!$this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers[] = $phoneNumber;
            $phoneNumber->setContact($this);
        }

        return $this;
    }

    public function removePhoneNumber(ContactPhoneNumber $phoneNumber): self
    {
        if ($this->phoneNumbers->contains($phoneNumber)) {
            $this->phoneNumbers->removeElement($phoneNumber);
            // set the owning side to null (unless already changed)
            if ($phoneNumber->getContact() === $this) {
                $phoneNumber->setContact(null);
            }
        }
        return $this;
    }
}
