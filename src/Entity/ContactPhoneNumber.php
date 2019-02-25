<?php

namespace App\Entity;

use App\Entity\Behaviours\Timestamps;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactPhoneNumberRepository")
 * @ORM\Table(name="contact_phone_number", indexes={
 *     @ORM\Index(columns={"number", "label"}, flags={"fulltext"})})
 * })
 * @ORM\HasLifecycleCallbacks()
 */
class ContactPhoneNumber
{
    use Timestamps;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Contact", inversedBy="phoneNumbers")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $contact;

    /**
     * @ORM\Column(name="number", type="phone_number")
     */
    private $number;

    /**
     * @ORM\Column(name="label", type="string", length=255, nullable=true)
     */
    private $label;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContact(): ?Contact
    {
        return $this->contact;
    }

    public function setContact(?Contact $contact): self
    {
        $this->contact = $contact;

        return $this;
    }

    public function getNumber(): PhoneNumber
    {
        return $this->number;
    }

    public function setNumber(PhoneNumber $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
