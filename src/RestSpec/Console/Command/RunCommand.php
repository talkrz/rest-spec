<?php

namespace RestSpec\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RestSpec\Spec;

class RunCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run')
            ->setDescription('Run validation of the spec against the API')
            ->addOption(
                'api',
                null,
                InputOption::VALUE_REQUIRED
            )
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
        $validator = new \RestSpec\Validator\Rest();
        $loader = new \RestSpec\Loader();
        $loader->run();

        try {
            $useCaseFilter = $input->getArgument('filter');
            $api = $input->getOption('api');
            $restSpec = Spec\Rest::getInstance();
            $restSpecValidator = new \RestSpec\Spec\Validator();
            $restSpecValidator->validate($restSpec);
            $report = $validator->validate($restSpec, $api, $useCaseFilter);

            $output->write($report->dumpAsConsoleText($api, $useCaseFilter));

            if ($report->getTotalUseCases() === 0 || $report->getUseCasesFailedCount() > 0) {
                exit(1);
            } else {
                exit(0);
            }
        } catch (\Exception $e) {
            $consoleOutput->getOutput()->writeln(sprintf(
                '<error>Whoops! Some unexpected error occured. The exception type is: %s, and a message is following:</error>',
                get_class($e)
            ));

            $consoleOutput->errorHandler($e, 2);
        }
    }
}
