<?php

namespace RestSpec\Validator;

use RestSpec\Spec;
use RestSpec\ValidationReport\UseCaseValidationReport;

class Response extends Validator
{
    /**
     * Validates actual response matches specification
     *
     * @param \GuzzleHttp\Message\Response $response
     * @param Spec\Response $responseSpec
     * @return bool
     */
    public function validate(\GuzzleHttp\Message\Response $response, Spec\Response $responseSpec, UseCaseValidationReport $report)
    {
        $this->validateStatusCode($response, $responseSpec, $report);

        if ($requiredHeaders = $responseSpec->getRequiredHeaders()) {
            foreach ($requiredHeaders as $headerName => $headerValue) {
                $actualHeader = $response->getHeader($headerName);

                if (!$actualHeader) {
                    $message = sprintf("\t\t<error>Response does not contain required header %s</error>", $headerName);
                    $this->addViolation($message);
                    $report->addHeadersViolation($message);
                } elseif ($actualHeader !== $headerValue) {
                    $message = sprintf("\t\t<error>The actual value of %s header is %s, but should be %s</error>",
                        $headerName,
                        $actualHeader,
                        $headerValue
                    );

                    $this->addViolation($message);
                    $report->addHeadersViolation($message);
                }
            }
        }

        switch ($responseSpec->getBodyType()) {
            case Spec\Response::BODY_TYPE_JSON:

                $bodyValidator = new Response\Body();
                $bodyValidator->validate($response, $responseSpec);
                $bodyViolations = $bodyValidator->getViolations();

                if ($bodyViolations) {
                    $this->addViolations($bodyViolations);
                    $report->addBodyViolations($bodyViolations);
                }

            break;
        }

        return $this->isValid();
    }

    private function validateStatusCode(\GuzzleHttp\Message\Response $response, Spec\Response $responseSpec, UseCaseValidationReport $report)
    {
        if ($responseSpec->getStatusCode()) {
            $expectedCode = $responseSpec->getStatusCode();
            $actualCode = $response->getStatusCode();

            if ($expectedCode !== $actualCode) {
                $message = sprintf("\t\t<error>Response code should be %s actual value is %s</error>",
                    $expectedCode, $actualCode
                );
                $this->addViolation($message);
                $report->setStatusCodeViolation($message);
            }
        }
    }
}
