<?php

namespace RestSpec\Validator\Response;

use GuzzleHttp\Message\Response;
use RestSpec\Spec;
use RestSpec\Validator\HasConsoleOutput;
use RestSpec\Validator\Response\Body\Json;

class Body
{
    use HasConsoleOutput;

    public function validate(Response $response, Spec\Response $responseSpec)
    {
        $output = $this->getOutput()->getOutput();

        $isValid = false;

        switch($responseSpec->getBodyType()) {
            case Spec\Response::BODY_TYPE_JSON:
                $jsonValidator = new Json($this->getOutput());

                $isValid = $jsonValidator->validate($response, $responseSpec);

                break;
            default:
                throw new \RuntimeException(sprintf('The specified response body type %s is not supported', $responseSpec->getBodyType()));
        }

        return $isValid;
    }
}
