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
        $faker = \Faker\Factory::create();
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        $contact = new Contact();
        $contact->setFirstName('Joan');
        $contact->setLastName('Doe');
        $contact->setEmailAddress('joan.doe@example.org');
        $contact->setFavourite(true);
        $imageLocalPath = $faker->image(__DIR__ . '/../../media', 400, 400, 'people');
        $contact->setProfilePhoto(pathinfo($imageLocalPath, PATHINFO_BASENAME));
        $contact->addPhoneNumber(
            (new ContactPhoneNumber())
                ->setNumber($phoneNumberUtil->parse('+38591234567'))
                ->setLabel('Mobile')
        );
        $manager->persist($contact);

        for ($i = 0; $i < 10; $i++) {
            $contact = new Contact();
            $contact->setFirstName($faker->firstName);
            $contact->setLastName($faker->lastName);
            $contact->setEmailAddress($faker->email);
            $contact->setFavourite($faker->randomElement([true, false]));
            $imageLocalPath = $faker->image(__DIR__ . '/../../media', 400, 400, 'people');
            $contact->setProfilePhoto(pathinfo($imageLocalPath, PATHINFO_BASENAME));
            $labels = ['Home', 'Work', 'Mobile', 'Cell', 'Relative'];
            shuffle($labels);
            for ($pn = 1; $pn <= $faker->numberBetween(1, 5); $pn++) {
                $fakePhoneNumber = $faker->phoneNumber;
                $contact->addPhoneNumber(
                    (new ContactPhoneNumber())
                        ->setNumber($phoneNumberUtil->parse($fakePhoneNumber, 'HR'))
                        ->setLabel(array_pop($labels))
                );
            }
            $manager->persist($contact);
        }

        $manager->flush();
    }
}
