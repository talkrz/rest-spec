<?php

namespace RestSpec\ValidationReport;

use RestSpec\Spec;
use RestSpec\Console\SpecView\UseCaseView;

class UseCaseValidationReport
{
    /**
     * @var Spec\UseCase
     */
    private $spec;

    /**
     * @var Spec\Response
     */
    private $responseSpec;

    private $headersViolations = [];

    private $statusCodeViolation = [];

    private $bodyViolations = [];

    /**
     * @var GuzzleHttp\Message\Response
     */
    private $response;

    public function __construct(Spec\UseCase $spec, Spec\Response $responseSpec)
    {
        $this->spec = $spec;
        $this->responseSpec = $responseSpec;
    }

    public function addBodyViolation($bodyViolation)
    {
        $this->bodyViolations[] = $bodyViolation;
    }

    public function addBodyViolations(array $bodyViolations)
    {
        $this->bodyViolations = array_merge($this->bodyViolations, $bodyViolations);
    }

    public function getBodyViolations()
    {
        return $this->bodyViolations;
    }

    public function addHeadersViolation($violation)
    {
        $this->headersViolations[] = $violation;
    }

    public function addHeadersViolations(array $violations)
    {
        $this->headersViolations = array_merge($this->headersViolations, $violations);
    }

    public function getHeadersViolations()
    {
        return $this->headersViolations;
    }

    public function setResponse(\GuzzleHttp\Message\Response $response)
    {
        $this->response = $response;
    }

    public function setStatusCodeViolation($violation)
    {
        $this->statusCodeViolation = $violation;
    }

    public function getStatusCodeViolation()
    {
        return $this->statusCodeViolation;
    }

    /**
     * @return GuzzleHttp\Message\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    public function dumpAsConsoleText()
    {
        $output = '';

        $useCaseView = new UseCaseView();
        $output .= $useCaseView->view($this->spec);

        if ($this->getStatusCodeViolation()) {
            $output .= $this->getStatusCodeViolation() . "\n";
        } else {
            $output .= sprintf("\t\tResponse code is <info>%s</info>\n", $this->getResponse()->getStatusCode());
        }

        if ($this->getHeadersViolations()) {
            foreach ($this->getHeadersViolations() as $violation) {
                $output .= $violation . "\n";
            }
        } else {
            $output .= sprintf("\t\tResponse has following required headers:\n");
            foreach ($this->responseSpec->getRequiredHeaders() as $headerName => $headerValue) {
                $output .= sprintf("\t\t\t<info>%s: %s</info>\n", $headerName, $headerValue);
            }
        }

        $output .= "\n";

        if (!$this->getBodyViolations()) {
            $actualBody = (string) $this->getResponse()->getBody();

            $json = json_decode($actualBody);

            if ($json) {
                $bodyStr = json_encode($json, JSON_PRETTY_PRINT);
            } else {
                $bodyStr = $json;
            }

            $output .= "\t\tResponse body is valid:\n\n\n";

            $output .= sprintf("<info>%s</info>\n\n\n", \RestSpec\Output\indentValue($bodyStr, 2));
        } else {
            foreach ($this->getBodyViolations() as $violation) {
                $output .= $violation . "\n";
            }
        }

        return $output;
    }
}
