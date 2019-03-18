<?php

namespace App\Tests\Command;

use App\Entity\ProfilePhoto;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use phpDocumentor\Reflection\File;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class SearchEnginePopulateCommandTest extends KernelTestCase
{
    public function testExecute()
    {

        $kernel = static::createKernel();
        $kernel->boot();
        $application = new Application($kernel);

        $command = $application->find('search-engine:populate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(['command' => $command->getName()]);

        $output = $commandTester->getDisplay();
        $this->assertRegExp('/Index creation acknowledged/', $output);
        $this->assertRegExp('/Populated index with (?:[2-9]|\d\d\d*) contacts/', $output);
    }
}
