<?php

namespace App\Command;

use App\Entity\Contact;
use App\Media\ImageThumbnailGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class GenerateImageThumbsCommand extends Command
{
    protected static $defaultName = 'generate-image-thumbs';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ImageThumbnailGenerator
     */
    private $thumbnailGenerator;

    public function __construct(EntityManagerInterface $entityManager, ImageThumbnailGenerator $thumbnailGenerator)
    {
        $this->entityManager = $entityManager;
        $this->thumbnailGenerator = $thumbnailGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Generates image thumbnails from source media directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->text('Generating image thumbnails...');

        /** @var Contact[] $contacts */
        $contacts = $this->entityManager->getRepository(Contact::class)->findAll();
        foreach ($contacts as $contact) {
            $thumbnailPath = $this->thumbnailGenerator->generate(
                'profile-photos/'.$contact->getProfilePhoto()->getFileName()
            );
            $io->writeln($thumbnailPath);
        }

        $io->success(sprintf('Done! Generated %d thumbnails.', count($contacts)));
    }
}
