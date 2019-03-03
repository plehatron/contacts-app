<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\ProfilePhoto;
use App\Form\ProfilePhotoType;
use App\Media\ImageThumbnailGenerator;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateProfilePhotoAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var RegistryInterface
     */
    private $doctrine;

    /**
     * @var FormFactoryInterface
     */
    private $factory;

    /**
     * @var ImageThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(
        RegistryInterface $doctrine,
        FormFactoryInterface $factory,
        ValidatorInterface $validator,
        ImageThumbnailGenerator $thumbnailGenerator

    ) {
        $this->validator = $validator;
        $this->doctrine = $doctrine;
        $this->factory = $factory;
        $this->thumbnailGenerator = $thumbnailGenerator;
    }

    /**
     * @param Request $request
     * @return ProfilePhoto
     * @throws \Gumlet\ImageResizeException
     */
    public function __invoke(Request $request): ProfilePhoto
    {
        $profilePhoto = new ProfilePhoto();

        $form = $this->factory->create(ProfilePhotoType::class, $profilePhoto);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->doctrine->getManager();
            $em->persist($profilePhoto);
            $em->flush();

            // Prevent the serialization of the file property
            $profilePhoto->setFile(null);

            $this->thumbnailGenerator->generate('profile-photos/' . $profilePhoto->getFileName());

            return $profilePhoto;
        }

        // This will be handled by API Platform and returns a validation error.
        throw new ValidationException($this->validator->validate($profilePhoto));
    }
}