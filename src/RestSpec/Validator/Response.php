<?php

namespace RestSpec\Validator;

use RestSpec\Spec;


class Response
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

        $isValid = true;

        if ($responseSpec->getStatusCode()) {
            $expectedCode = $responseSpec->getStatusCode();
            $actualCode = $response->getStatusCode();


            if ($expectedCode === $actualCode) {
                $output->writeln(sprintf("\t\tResponse code is <info>%s</info>", $actualCode));
            } else {
                $output->writeln(
                    sprintf("\t\t<error>Response code should be %s actual value is %s</error>",
                    $expectedCode, $actualCode
                ));
                $isValid = false;
            }
        }

        if ($requiredHeaders = $responseSpec->getRequiredHeaders()) {
            foreach($requiredHeaders as $headerName => $headerValue) {
                $actualHeader = $response->getHeader($headerName);

                if (!$actualHeader) {
                    $output->writeln(sprintf("\t\t<error>Response does not contain required header %s</error>", $headerName));
                    $isValid = false;
                } else if ($actualHeader !== $headerValue) {
                    $output->writeln(
                        sprintf("\t\t<error>The actual value of %s header is %s, but should be %s</error>",
                        $headerName,
                        $actualHeader,
                        $headerValue
                    ));
                    $isValid = false;
                }
            }

            if ($isValid) {
                $output->writeln(sprintf("\t\tResponse has following required headers:"));
                foreach($requiredHeaders as $headerName => $headerValue) {
                    $output->writeln(sprintf("\t\t\t<info>%s: %s</info>", $headerName, $headerValue));
                }
            }
        }

        if ($responseSpec->getBodyType()) {
            $bodyValidator = new Response\Body($this->getOutput());
            $isValid = $bodyValidator->validate($response, $responseSpec);
        }

        return $isValid;
    }
}
