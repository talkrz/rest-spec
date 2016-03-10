<?php

namespace RestSpec\Validator;

use RestSpec\Spec;
use RestSpec\ValidationReport\ValidationReport;
use RestSpec\ValidationReport\ApiValidationReport;
use RestSpec\ValidationReport\UrlValidationReport;
use RestSpec\ValidationReport\UseCaseValidationReport;

class Rest
{
    /**
     * @todo A monster method to refactor!!!
     *
     * @param  SpecRest $restSpec
     * @param  string   $useCaseFilter
     * @return ValidationReport
     */
    public function validate(Spec\Rest $restSpec, $apiFilter, $useCaseFilter = null)
    {
        $validationReport = new ValidationReport();

        $apiSpecs = $restSpec->getApiSpecs();

        foreach ($apiSpecs as $apiSpec) {
            if ($apiFilter && $apiSpec->getName() !== $apiFilter) {
                continue;
            }

            $apiValidationReport = new ApiValidationReport($apiSpec);
            $validationReport->addApiReport($apiValidationReport);
            $client = new \GuzzleHttp\Client([
                'base_url' => $apiSpec->getBaseUrl(),
            ]);

            $responseValidator = new Response();

            foreach ($apiSpec->getUrlSpecs() as $urlSpec) {
                $urlReport = new UrlValidationReport($urlSpec);
                $apiValidationReport->addUrlReport($urlReport);

                $useCases = $urlSpec->getUseCases();

                foreach ($useCases as $urlUseCaseSpec) {
                    if ($useCaseFilter && strpos(strtolower($urlUseCaseSpec->getDescription()), strtolower($useCaseFilter)) === false) {
                        continue;
                    }

                    $useCaseValidationReport = new UseCaseValidationReport(
                        $urlUseCaseSpec,
                        $urlUseCaseSpec->getExpectedResponseSpec()
                    );

                    $urlReport->addUseCaseReport($useCaseValidationReport);

                    if ($beforeCallback = $urlUseCaseSpec->getBeforeCallback()) {
                        call_user_func($beforeCallback, $urlUseCaseSpec);
                    }

                    $request = $urlUseCaseSpec->getRequest();
                    $res = $client->send($request);

                    $responseValidator->validate(
                        $res,
                        $urlUseCaseSpec->getExpectedResponseSpec(),
                        $useCaseValidationReport
                    );

                    $useCaseValidationReport->setResponse($res);

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

        return $validationReport;
    }
}
