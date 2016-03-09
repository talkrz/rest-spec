<?php

namespace RestSpec\Validator\Response;

use GuzzleHttp\Message\Response;
use RestSpec\Spec;
use RestSpec\Validator\HasConsoleOutput;
use RestSpec\Validator\Response\Body\Json;
use RestSpec\Validator\Validator;

class Body extends Validator
{
    use HasConsoleOutput;

    public function validate(Response $response, Spec\Response $responseSpec)
    {
        $output = $this->getOutput()->getOutput();

        switch ($responseSpec->getBodyType()) {
            case Spec\Response::BODY_TYPE_JSON:
                $jsonValidator = new Json();

                $jsonValidator->validate($response, $responseSpec);

                $this->addViolations($jsonValidator->getViolations());

                break;
            default:
                throw new \RuntimeException(sprintf('The specified response body type %s is not supported', $responseSpec->getBodyType()));
        }

        return $this->isValid();
    }
}
