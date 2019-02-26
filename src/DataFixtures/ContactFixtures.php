<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\ContactPhoneNumber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use libphonenumber\PhoneNumberUtil;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $contact = new Contact();
        $contact->setFirstName('Joan');
        $contact->setLastName('Doe');
        $contact->setEmailAddress('joan.doe@example.org');
        $contact->setFavourite(true);

        $phoneNumberUtil = PhoneNumberUtil::getInstance();
        $contact->addPhoneNumber(
            (new ContactPhoneNumber())
                ->setNumber($phoneNumberUtil->parse('+38591234567'))
                ->setLabel('Mobile')
        );

        $manager->persist($contact);

        $manager->flush();
    }
}
