<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use App\Entity\ProfilePhoto;
use libphonenumber\PhoneNumber;
use PHPUnit\Framework\TestCase;

class ContactTest extends TestCase
{
    public function testSetters()
    {
        $profilePhoto = new ProfilePhoto();
        $phoneNumbers = [new PhoneNumber()];
        $dateTime = new \DateTime();

        $contact = new Contact();
        $contact->setProfilePhoto($profilePhoto);
        $contact->setPhoneNumbers($phoneNumbers);
        $contact->setCreatedAt($dateTime);
        $contact->setUpdatedAt($dateTime);

        $this->assertEquals($contact->getProfilePhoto(), $profilePhoto);
        $this->assertEquals($phoneNumbers[0], $contact->getPhoneNumbers()->first());
        $this->assertEquals($dateTime, $contact->getCreatedAt());
        $this->assertEquals($dateTime, $contact->getUpdatedAt());
    }
}