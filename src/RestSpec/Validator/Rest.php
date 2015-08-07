<?php

namespace RestSpec\Validator;

use RestSpec\Output\Formatter;
use RestSpec\Spec;

class Rest
{
    use HasConsoleOutput;

    private $useCasesPassedCount = 0;

    private $useCasesFailedCount = 0;

    /**
     * @todo A monster method to refactor!!!
     *
     * @param  SpecRest $restSpec
     * @return void
     */
    public function validate(Spec\Rest $restSpec)
    {
        $apiSpec = $restSpec->getApiSpecs();

        if (empty($apiSpec)) {
            throw new \RuntimeException('No API spec provided');
        }

        foreach($apiSpec as $apiSpec)
        {
            $client = new \GuzzleHttp\Client([
                'base_url' => $apiSpec->getBaseUrl(),
            ]);

            $output = $this->getOutput()->getOutput();

            $output->writeln(sprintf("Specification of API at: <info>%s</info>\n", $apiSpec->getBaseUrl()));

            $responseValidator = new Response($this->getOutput());

            foreach($apiSpec->getUrlSpecs() as $urlSpec) {

                $output->writeln(sprintf("<comment>%s</comment>\n", $urlSpec->getDescription()));

                $useCases = $urlSpec->getUseCases();

                if (!$useCases) {
                    throw new \RuntimeException('You have to specify use cases inside the URL specificetion');
                }

                foreach($useCases as $urlUseCaseSpec) {
                    $output->writeln(sprintf("\t<options=bold>%s</options=bold>\n", $urlUseCaseSpec->getDescription()));

                    $request = $urlUseCaseSpec->getRequest();

                    if (!$request) {
                        throw new \RuntimeException('You have to add request specification using givenRequest() function');
                    }

                    $exampleUrl = '';
                    if ($urlUseCaseSpec->isATemplate()) {

                        $exampleUrl = sprintf("\t(example URL: <info>%s</info>)", $urlUseCaseSpec->getExampleUrl());
                    }

                    $output->writeln(sprintf("\t<info>%s %s</info>%s\n", $request->getMethod(), $urlSpec->getUrl(), $exampleUrl));

                    $res = $client->send($request);

                    $expectedResponseSpec = $urlUseCaseSpec->getExpectedResponseSpec();

                    if (!$expectedResponseSpec) {
                        throw new \RuntimeException('You have to specify expected response using expectResponse() function');
                    }

                    $responseValidator->validate($res, $expectedResponseSpec);

                    $output->write(PHP_EOL);

                    if ($responseValidator->isValid()) {
                        ++$this->useCasesPassedCount;
                    } else {
                        ++$this->useCasesFailedCount;
                    }

                    $responseValidator->reset();

                    if ($doneCallback = $urlUseCaseSpec->getDoneCallback()) {
                        call_user_func($doneCallback);
                    }
                }
            }

            $output->write(sprintf(
                'Tested %d use cases. (<info>Passed: %d</info>',
                $this->useCasesPassedCount + $this->useCasesFailedCount,
                $this->useCasesPassedCount
            ));

            if ($this->useCasesFailedCount > 0) {
                $output->writeln(sprintf(', <error>Failed: %d</error>)', $this->useCasesFailedCount));
                exit(1);
            } else {
                $output->writeln(')');
                exit(0);
            }
        }
    }
}
