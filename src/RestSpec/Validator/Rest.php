<?php

namespace RestSpec\Validator;

use RestSpec\Output\Formatter;
use RestSpec\Spec;
use RestSpec\Console\SpecView\UseCaseView;
use RestSpec\ValidationReport\ValidationReport;

class Rest
{
    use HasConsoleOutput;

    /**
     * @todo A monster method to refactor!!!
     *
     * @param  SpecRest $restSpec
     * @param  string   $useCaseFilter
     * @return void
     */
    public function validate(Spec\Rest $restSpec, $apiFilter, $useCaseFilter = null)
    {
        $output = $this->getOutput()->getOutput();
        $validationReport = new ValidationReport($this->getOutput());

        $apiSpecs = $restSpec->getApiSpecs();



        foreach ($apiSpecs as $apiSpec) {
            if ($apiFilter && $apiSpec->getName() !== $apiFilter) {
                continue;
            }
            $client = new \GuzzleHttp\Client([
                'base_url' => $apiSpec->getBaseUrl(),
            ]);

            $output->writeln(sprintf("\nAPI base URL: <info>%s</info>\n", $apiSpec->getBaseUrl()));

            $responseValidator = new Response($this->getOutput());

            foreach ($apiSpec->getUrlSpecs() as $urlSpec) {
                $output->writeln(sprintf("<comment>%s</comment>\n\n<info>%s</info>\n", $urlSpec->getDescription(), $urlSpec->getUrl()));

                $useCases = $urlSpec->getUseCases();

                foreach ($useCases as $urlUseCaseSpec) {
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
                        $validationReport->incrementPassedCount();
                    } else {
                        $validationReport->incrementFailedCount();
                    }

                    $responseValidator->reset();

                    if ($doneCallback = $urlUseCaseSpec->getDoneCallback()) {
                        call_user_func($doneCallback, $res);
                    }
                }
            }
        }

        $validationReport->dumpAsConsoleText($apiFilter, $useCaseFilter);

        if ($validationReport->getTotalUseCases() === 0 || $validationReport->getUseCasesFailedCount() > 0) {
            exit(1);
        } else {
            exit(0);
        }
    }
}
