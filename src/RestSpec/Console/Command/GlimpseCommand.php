<?php

namespace RestSpec\Console\Command;

use Symfony\Component\Console\Command\Command,
    Symfony\Component\Console\Input\InputInterface,
    Symfony\Component\Console\Output\OutputInterface,
    RestSpec\Spec,
    RestSpec\Console\SpecView\UseCaseView;

class GlimpseCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('glimpse')
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

        foreach($apiSpec as $apiSpec)
        {
            $output->writeln(sprintf("\nAPI base URL: <info>%s</info>\n", $apiSpec->getBaseUrl()));

            foreach($apiSpec->getUrlSpecs() as $urlSpec) {

                $output->writeln(sprintf("<comment>%s</comment>\n\n<info>%s</info>\n", $urlSpec->getDescription(), $urlSpec->getUrl()));

                $useCases = $urlSpec->getUseCases();

                $useCaseView = new UseCaseView();
                foreach($useCases as $useCase) {
                    $useCaseView->view($useCase, $output);
                }
            }
        }
    }
}
