<?php

namespace App\Command;

use App\Elasticsearch\Index\ContactIndex;
use App\Entity\Contact;
use App\EventListener\ContactSearchEngineIndexer;
use Doctrine\ORM\EntityManagerInterface;
use Elasticsearch\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class SearchEnginePopulateCommand extends Command
{
    protected static $defaultName = 'search-engine:populate';

    /**
     * @var Client
     */
    private $client;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ContactSearchEngineIndexer
     */
    private $contactSearchEngineIndexer;

    /**
     * @var ContactIndex
     */
    private $contactIndex;

    public function __construct(
        Client $client,
        EntityManagerInterface $entityManager,
        ContactSearchEngineIndexer $contactSearchEngineIndexer,
        ContactIndex $contactIndex
    ) {
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->contactSearchEngineIndexer = $contactSearchEngineIndexer;
        $this->contactIndex = $contactIndex;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Populates Elasticsearch indexes');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $indexName = $this->contactIndex->getName();

        // Create index
        if ($this->client->indices()->exists(['index' => $indexName])) {
            $io->writeln('Recreating contact index...');
            $this->client->indices()->delete(['index' => $indexName]);
        }

        $params = [
            'index' => $indexName,
            'body' => [
                'mappings' => $this->contactIndex->getMappings(),
            ],
        ];
        $response = $this->client->indices()->create($params);
        if ($response['acknowledged']) {
            $io->success(sprintf('Index creation acknowledged'));
        }

        // Populate with data
        $repository = $this->entityManager->getRepository(Contact::class);
        /** @var Contact[] $contacts */
        $contacts = $repository->findAll();
        foreach ($contacts as $contact) {
            $this->contactSearchEngineIndexer->index($contact);
        }
        $io->success(sprintf('Populated index with %d contacts', count($contacts)));
    }
}
