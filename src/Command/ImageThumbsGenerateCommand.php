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

class ImageThumbsGenerateCommand extends Command
{
    protected static $defaultName = 'image-thumbs:generate';

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ImageThumbnailGenerator
     */
    private $thumbnailGenerator;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        EntityManagerInterface $entityManager,
        ImageThumbnailGenerator $thumbnailGenerator,
        Filesystem $filesystem
    ) {
        $this->entityManager = $entityManager;
        $this->thumbnailGenerator = $thumbnailGenerator;
        $this->filesystem = $filesystem;

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

        $counter = 0;

        /** @var Contact[] $contacts */
        $contacts = $this->entityManager->getRepository(Contact::class)->findAll();
        foreach ($contacts as $contact) {
            $sourceFileName = 'profile-photos/'.$contact->getProfilePhoto()->getFileName();
            $thumbnailPath = $this->thumbnailGenerator->generate($sourceFileName);
            $io->writeln($thumbnailPath);
            $counter++;
        }

        $io->success(sprintf('Done! Generated %d thumbnails.', $counter));
    }
}
