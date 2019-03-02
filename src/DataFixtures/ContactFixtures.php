<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\ContactPhoneNumber;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create();

        $contact = new Contact();
        $contact->setFirstName('Joan');
        $contact->setLastName('Doe');
        $contact->setEmailAddress('joan.doe@example.org');
        $contact->setFavourite(true);
        $imageLocalPath = $faker->image(__DIR__.'/../../media', 400, 400, 'people');
        $contact->setProfilePhoto(pathinfo($imageLocalPath, PATHINFO_BASENAME));
        $contact->addPhoneNumber(
            (new ContactPhoneNumber())
                ->setNumber('+385912345678')
                ->setLabel('Mobile')
        );
        $manager->persist($contact);

        $fakePhoneNumbers = [
            '+33416483558',
            '+46330044739',
            '+622283210194',
            '+14013315588',
        ];

        for ($i = 0; $i < 10; $i++) {
            $contact = new Contact();
            $contact->setFirstName($faker->firstName);
            $contact->setLastName($faker->lastName);
            $contact->setEmailAddress($faker->email);
            $contact->setFavourite($faker->randomElement([true, false]));
            $imageLocalPath = $faker->image(__DIR__.'/../../media', 400, 400, 'people');
            $contact->setProfilePhoto(pathinfo($imageLocalPath, PATHINFO_BASENAME));
            $labels = ['Home', 'Work', 'Mobile', 'Cell', 'Relative'];
            shuffle($labels);
            for ($pn = 1; $pn <= $faker->numberBetween(1, 5); $pn++) {
                $fakePhoneNumber = $faker->randomElement($fakePhoneNumbers);
                $contact->addPhoneNumber(
                    (new ContactPhoneNumber())
                        ->setNumber($fakePhoneNumber)
                        ->setLabel(array_pop($labels))
                );
            }
            $manager->persist($contact);
        }

        $manager->flush();
    }
}
