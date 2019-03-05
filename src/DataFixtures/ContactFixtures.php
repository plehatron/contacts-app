<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use App\Entity\ContactPhoneNumber;
use App\Entity\ProfilePhoto;
use App\Media\ImageThumbnailGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class ContactFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    private $faker;

    /**
     * @var ImageThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(ImageThumbnailGenerator $thumbnailGenerator)
    {
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    public function load(ObjectManager $manager)
    {
        $this->faker = \Faker\Factory::create();

        $contact = $this->makeContactEntity(
            'Joan',
            'Doe',
            'joan.doe@example.org',
            true,
            ['+385912345678'],
            ['Mobile']
        );
        $manager->persist($contact);

        // Not using Faker for phone numbers because it generates invalid E164 phone numbers
        $fakePhoneNumbers = [
            '+33416483558',
            '+46330044739',
            '+622283210194',
            '+14013315588',
        ];
        $fakePhoneNumberLabels = ['Home', 'Work', 'Mobile', 'Cell', 'Relative'];

        for ($i = 0; $i < 10; $i++) {
            $contact = $this->makeContactEntity(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->email,
                $this->faker->randomElement([true, false]),
                $fakePhoneNumbers,
                $fakePhoneNumberLabels
            );
            $manager->persist($contact);
        }

        $manager->flush();
    }

    private function makeContactEntity(
        string $firstName,
        string $lastName,
        string $email,
        bool $favourite,
        array $fakePhoneNumbers,
        array $fakePhoneNumberLabels
    ): Contact {
        $contact = new Contact();
        $contact->setFirstName($firstName);
        $contact->setLastName($lastName);
        $contact->setEmailAddress($email);
        $contact->setFavourite($favourite);
        $imageLocalPath = $this->faker->image(__DIR__.'/../../media/profile-photos', 400, 400, 'people');
        $profilePhoto = new ProfilePhoto();
        $profilePhoto->setFileName(pathinfo($imageLocalPath, PATHINFO_BASENAME));
        $this->thumbnailGenerator->generate('profile-photos/'.$profilePhoto->getFileName());
        $contact->setProfilePhoto($profilePhoto);
        $localFakePhoneNumberLabels = $fakePhoneNumberLabels;
        shuffle($localFakePhoneNumberLabels);
        for ($pn = 1; $pn <= $this->faker->numberBetween(1, 5); $pn++) {
            $fakePhoneNumber = $this->faker->randomElement($fakePhoneNumbers);
            $contact->addPhoneNumber(
                (new ContactPhoneNumber())
                    ->setNumber($fakePhoneNumber)
                    ->setLabel(array_pop($localFakePhoneNumberLabels))
            );
        }

        return $contact;
    }
}
