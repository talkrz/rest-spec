<?php

namespace RestSpec\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelpCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('help')
            ->setDescription('Usage information')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("Usage:");
        $output->writeln("<info>\trest-spec run [use-case-filter]</info> run validation of the API");
    }
}
