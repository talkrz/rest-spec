<?php

namespace RestSpec\Console\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    RestSpec\Spec;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run validation of the spec against the API')
            ->addArgument(
                'filter',
                InputArgument::OPTIONAL,
                'Run only use cases that match filter'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $constraintDescriber = new \RestSpec\Output\ConstraintDescriber();

        $consoleOutput = new \RestSpec\Output\ConsoleOutput($output, $constraintDescriber);
        $validator = new \RestSpec\Validator\Rest($consoleOutput);

        $defaultSpecDirectoryName = 'rest-spec';
        $specDirectory = dirname(COMPOSER_INSTALL) . '/../' . $defaultSpecDirectoryName . '/';
        $i = new \DirectoryIterator($specDirectory);
        foreach($i as $file) {
            if (!$file->isDot()) {
                $filename = $i->getPathname();
                require $filename;
            }
        }

        try {
            $useCaseFilter = $input->getArgument('filter');
            $validator->validate(Spec\Rest::getInstance(), $useCaseFilter);
        } catch(\Exception $e) {
            $consoleOutput->getOutput()->writeln(sprintf(
                '<error>Whoops! Some unexpected error occured. The exception type is: %s, and a message is following:</error>',
                get_class($e)
            ));

            $consoleOutput->errorHandler($e, 2);
        }
    }
}
