<?php

namespace RestSpec\Validator;

use RestSpec\Output\Formatter;
use RestSpec\Spec;

class Rest
{
    use HasConsoleOutput;

    private $useCasesPassedCount = 0;

    private $useCasesFailedCount = 0;

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

                foreach($urlSpec->getUseCases() as $urlUseCaseSpec) {
                    $output->writeln(sprintf("\t<options=bold>%s</options=bold>\n", $urlUseCaseSpec->getDescription()));

                    $request = $urlUseCaseSpec->getRequest();

                    $output->writeln(sprintf("\t<info>%s /%s</info>\n", $request->getMethod(), $urlSpec->getUrl()));

                    $res = $client->send($request);

                    $expectedResponseSpec = $urlUseCaseSpec->getExpectedResponseSpec();

                    $responseValidator->validate($res, $expectedResponseSpec);

                    $output->write(PHP_EOL);

                    if ($responseValidator->isValid()) {
                        ++$this->useCasesPassedCount;
                    } else {
                        ++$this->useCasesFailedCount;
                    }

                    $responseValidator->reset();
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
