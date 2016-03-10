<?php

namespace RestSpec\ValidationReport;

use RestSpec\Validator\HasConsoleOutput;
use RestSpec\Spec;
use RestSpec\Output\ConsoleOutput;
use RestSpec\Console\SpecView\UseCaseView;

class UseCaseValidationReport
{
    use HasConsoleOutput;

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

    public function __construct(Spec\UseCase $spec, Spec\Response $responseSpec, ConsoleOutput $output)
    {
        $this->spec = $spec;
        $this->responseSpec = $responseSpec;
        $this->output = $output;
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
        $output = $this->getOutput()->getOutput();

        $useCaseView = new UseCaseView();
        $useCaseView->view($this->spec, $output);

        if ($this->getStatusCodeViolation()) {
            $output->writeln($this->getStatusCodeViolation());
        } else {
            $output->writeln(sprintf("\t\tResponse code is <info>%s</info>", $this->getResponse()->getStatusCode()));
        }

        if ($this->getHeadersViolations()) {
            foreach ($this->getHeadersViolations() as $violation) {
                $output->writeln($violation);
            }
        } else {
            $output->writeln(sprintf("\t\tResponse has following required headers:"));
            foreach ($this->responseSpec->getRequiredHeaders() as $headerName => $headerValue) {
                $output->writeln(sprintf("\t\t\t<info>%s: %s</info>", $headerName, $headerValue));
            }
        }

        $output->writeln('');

        if (!$this->getBodyViolations()) {
            $actualBody = (string) $this->getResponse()->getBody();

            $json = json_decode($actualBody);

            if ($json) {
                $bodyStr = json_encode($json, JSON_PRETTY_PRINT);
            } else {
                $bodyStr = $json;
            }

            $output->writeln("\t\tResponse body is valid:\n\n");

            $output->writeln(sprintf("<info>%s</info>\n\n", \RestSpec\Output\indentValue($bodyStr, 2)));
        } else {
            foreach ($this->getBodyViolations() as $violation) {
                $output->writeln($violation);
            }
        }
    }
}
