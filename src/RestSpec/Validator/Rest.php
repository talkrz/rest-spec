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
     * @param  string   $useCaseFilter
     * @return void
     */
    public function validate(Spec\Rest $restSpec, $useCaseFilter = null)
    {
        $apiSpec = $restSpec->getApiSpecs();

        foreach($apiSpec as $apiSpec)
        {
            $client = new \GuzzleHttp\Client([
                'base_url' => $apiSpec->getBaseUrl(),
            ]);

            $output = $this->getOutput()->getOutput();

            $output->writeln(sprintf("API base URL: <info>%s</info>\n", $apiSpec->getBaseUrl()));

            $responseValidator = new Response($this->getOutput());

            foreach($apiSpec->getUrlSpecs() as $urlSpec) {

                $output->writeln(sprintf("<comment>%s</comment>\n", $urlSpec->getDescription()));

                $useCases = $urlSpec->getUseCases();

                foreach($useCases as $urlUseCaseSpec) {

                    if ($useCaseFilter && strpos($urlUseCaseSpec->getDescription(), $useCaseFilter) === false) {
                        continue;
                    }

                    if ($beforeCallback = $urlUseCaseSpec->getBeforeCallback()) {
                        call_user_func($beforeCallback, $urlUseCaseSpec);
                    }

                    $request = $urlUseCaseSpec->getRequest();

                    $this->printUseCaseInfo($urlUseCaseSpec);

                    $res = $client->send($request);

                    $expectedResponseSpec = $urlUseCaseSpec->getExpectedResponseSpec();

                    $responseValidator->validate($res, $expectedResponseSpec);

                    if ($responseValidator->isValid()) {
                        ++$this->useCasesPassedCount;
                    } else {
                        ++$this->useCasesFailedCount;
                    }

                    $responseValidator->reset();

                    if ($doneCallback = $urlUseCaseSpec->getDoneCallback()) {
                        call_user_func($doneCallback, $res);
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

    /**
     * Display human readable info about URL use case
     *
     * @param  Spec\UseCase $useCase an URL use case specification
     * @return void
     */
    private function printUseCaseInfo(Spec\UseCase $useCase)
    {
        $output = $this->getOutput()->getOutput();
        $request = $useCase->getRequest();

        $output->writeln(sprintf("\t<options=bold>%s</options=bold>\n", $useCase->getDescription()));

        $exampleUrl = '';
        if ($useCase->isATemplate()) {
            $exampleUrl = sprintf("\t(example URL: <info>%s</info>)", $useCase->getExampleUrl());
        }

        $output->writeln(sprintf("\t<info>%s %s</info>%s\n", $request->getMethod(), $useCase->getUrl(), $exampleUrl));

        if ($queryParameters = (string) $request->getQuery()) {
            $output->writeln(sprintf("\tRequest query: <info>%s</info>\n\n", \RestSpec\Output\indentValue($queryParameters, 1)));
        }

        if ($headers = $request->getHeaders()) {
            $output->writeln("\tRequest headers:");

            foreach($headers as $headerName => $headerValue) {
                $output->writeln(sprintf("\t\t<info>%s: %s</info>", $headerName, join('; ', $headerValue)));
            }

            $output->writeln('');
        }

        if ($body = (string) $request->getBody()) {
            $json = json_decode($body);

            if ($json) {
                $bodyStr = json_encode($json, JSON_PRETTY_PRINT);
            } else {
                $bodyStr = $json;
            }

            $output->writeln(sprintf("\tRequest body:\n<info>%s</info>\n\n", \RestSpec\Output\indentValue($bodyStr, 1)));
        }

        $output->write(PHP_EOL);
    }
}
