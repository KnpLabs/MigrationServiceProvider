<?php

namespace Knp\Command;

use Knp\Command\Command;

use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command
{
    public function configure()
    {
        $this->setName('knp:migration:migrate');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app        = $this->getSilexApplication();
        $manager    = $app['migration'];

        if (!$manager->hasVersionInfo()) {
            $manager->createVersionInfo();
        }

        $res = $manager->migrate();

        switch($res) {
            case true:
                $output->writeln(sprintf('Succesfully executed <info>%d</info> migration(s)!', $manager->getMigrationExecuted()));
                foreach ($manager->getMigrationInfos() as $info) {
                    $output->writeln(sprintf(' - <info>%s</info>', $info));
                }
                break;
            case null:
                $output->writeln('No migrations to execute, you are up to date!');
                break;
        }
    }
}