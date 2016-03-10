<?php

namespace RestSpec\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use RestSpec\Spec;
use RestSpec\Console\SpecView\UseCaseView;

class PreviewCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('preview')
            ->setDescription('View brief information about the API')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $constraintDescriber = new \RestSpec\Output\ConstraintDescriber();
        $consoleOutput = new \RestSpec\Output\ConsoleOutput($output, $constraintDescriber);

        $loader = new \RestSpec\Loader();
        $loader->run();

        $restSpec = Spec\Rest::getInstance();
        $restSpecValidator = new \RestSpec\Spec\Validator();
        $restSpecValidator->validate($restSpec);

        $apiSpec = $restSpec->getApiSpecs();

        foreach ($apiSpec as $apiSpec) {
            $output->writeln(sprintf("\nAPI base URL: <info>%s</info>\n", $apiSpec->getBaseUrl()));

            foreach ($apiSpec->getUrlSpecs() as $urlSpec) {
                $output->writeln(sprintf("<comment>%s</comment>\n\n<info>%s</info>\n", $urlSpec->getDescription(), $urlSpec->getUrl()));

                $useCases = $urlSpec->getUseCases();

                $useCaseView = new UseCaseView();
                foreach ($useCases as $useCase) {
                    $output->write($useCaseView->view($useCase, $output));
                }
            }
        }
    }
}
