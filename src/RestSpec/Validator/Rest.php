<?php

namespace RestSpec\Validator;

use RestSpec\Output\Formatter;
use RestSpec\Spec;
use RestSpec\Console\SpecView\UseCaseView;

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

            $output->writeln(sprintf("\nAPI base URL: <info>%s</info>\n", $apiSpec->getBaseUrl()));

            $responseValidator = new Response($this->getOutput());

            foreach($apiSpec->getUrlSpecs() as $urlSpec) {

                $output->writeln(sprintf("<comment>%s</comment>\n\n<info>%s</info>\n", $urlSpec->getDescription(), $urlSpec->getUrl()));

                $useCases = $urlSpec->getUseCases();

                foreach($useCases as $urlUseCaseSpec) {

                    if ($useCaseFilter && strpos(strtolower($urlUseCaseSpec->getDescription()), strtolower($useCaseFilter)) === false) {
                        continue;
                    }

                    if ($beforeCallback = $urlUseCaseSpec->getBeforeCallback()) {
                        call_user_func($beforeCallback, $urlUseCaseSpec);
                    }

                    $request = $urlUseCaseSpec->getRequest();

                    $useCaseView = new UseCaseView();
                    $useCaseView->view($urlUseCaseSpec, $output);

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
}
