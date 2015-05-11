<?php

namespace RestSpec\Validator;

use RestSpec\Spec;


class Response extends Validator
{
    use HasConsoleOutput;

    /**
     * Validates actual response matches specification
     *
     * @param \GuzzleHttp\Message\Response $response
     * @param Spec\Response $responseSpec
     * @return bool
     */
    public function validate(\GuzzleHttp\Message\Response $response, Spec\Response $responseSpec)
    {
        $output = $this->getOutput()->getOutput();

        $this->validateStatusCode($response, $responseSpec);

        if ($requiredHeaders = $responseSpec->getRequiredHeaders()) {
            foreach($requiredHeaders as $headerName => $headerValue) {
                $actualHeader = $response->getHeader($headerName);

                if (!$actualHeader) {
                    $output->writeln(sprintf("\t\t<error>Response does not contain required header %s</error>", $headerName));
                } else if ($actualHeader !== $headerValue) {
                    $message = sprintf("\t\t<error>The actual value of %s header is %s, but should be %s</error>",
                        $headerName,
                        $actualHeader,
                        $headerValue
                    );
                    $output->writeln($message);
                    $this->addViolation($message);
                }
            }

            if ($this->isValid()) {
                $output->writeln(sprintf("\t\tResponse has following required headers:"));
                foreach($requiredHeaders as $headerName => $headerValue) {
                    $output->writeln(sprintf("\t\t\t<info>%s: %s</info>", $headerName, $headerValue));
                }
            }
        }

        if ($responseSpec->getBodyType()) {
            $bodyValidator = new Response\Body($this->getOutput());
            $bodyValidator->validate($response, $responseSpec);
            $this->addViolations($bodyValidator->getViolations());
        }

        return $this->isValid();
    }

    private function validateStatusCode(\GuzzleHttp\Message\Response $response, Spec\Response $responseSpec)
    {
        $output = $this->getOutput()->getOutput();

        if ($responseSpec->getStatusCode()) {
            $expectedCode = $responseSpec->getStatusCode();
            $actualCode = $response->getStatusCode();


            if ($expectedCode === $actualCode) {
                $output->writeln(sprintf("\t\tResponse code is <info>%s</info>", $actualCode));
            } else {
                $message = sprintf("\t\t<error>Response code should be %s actual value is %s</error>",
                    $expectedCode, $actualCode
                );
                $output->writeln($message);
                $this->addViolation($message);
            }
        }
    }
}
