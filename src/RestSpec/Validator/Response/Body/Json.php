<?php

namespace RestSpec\Validator\Response\Body;

use GuzzleHttp\Message\Response;
use RestSpec\Spec;
use RestSpec\Validator\Validator;
use Symfony\Component\Validator\Validation;

class Json extends Validator
{

    const JSON_OUTPUT_MAX_LENGTH = 20000;

    /**
     * Yet another monstrous method to refactor ;)
     *
     * @param  Response     $response
     * @param  SpecResponse $responseSpec
     * @return boolean
     */
    public function validate(Response $response, Spec\Response $responseSpec)
    {
        // first validate whether it is really a JSON
        $actualBodyData = json_decode((string) $response->getBody(), true);

        if ($actualBodyData === null) {
            $message = sprintf(
                "Response body is not a valid JSON. Actual response body is:\n\n%s",
                strip_tags((string) $response->getBody())
            );

            $message = \RestSpec\Output\textBox($message, function ($line) {
                return '<error>' . $line . '</error>';
            }, 3);

            $this->addViolation($message);
        }

        // then if exact body specified check it matches spec
        if ($responseSpec->getBody()) {
            $this->validateBody($response, $responseSpec);
        } elseif ($constraint = $responseSpec->getBodyConstraint()) {
            $validator = Validation::createValidator();

            try {
                $violations = $validator->validateValue($actualBodyData, $constraint);

                if ($violations->count()) {
                    $violationsDescription = '';
                    foreach ($violations as $violation) {
                        $violationsDescription .= $violation . PHP_EOL;
                    }

                    $encodedJson = json_encode($actualBodyData, \JSON_PRETTY_PRINT);

                    if (strlen($encodedJson) > self::JSON_OUTPUT_MAX_LENGTH) {
                        $encodedJson = substr($encodedJson, 0, self::JSON_OUTPUT_MAX_LENGTH);
                        $encodedJson .= "\n\n ... this JSON is too large to print all of it";
                    }

                    $message = sprintf(
                        "Response body violates constraint:\n%s\nActual response body is:\n\n%s",
                        $violationsDescription,
                        $encodedJson
                    );

                    $message = \RestSpec\Output\textBox($message, function ($line) {
                        return '<error>' . $line . '</error>';
                    }, 3);

                    $this->addViolation($message);
                }
            } catch (\Symfony\Component\Validator\Exception\UnexpectedTypeException $e) {
                $message = sprintf(
                    "\t\t<error>The type of body response is invalid, actual value: %s</error>\n\t\t<error>(common mistake: you expect a collection but get a single result)</error>\n", (string) $response->getBody()
                );

                $this->addViolation($message);
            }
        }

        return $this->isValid();
    }

    public function validateBody(Response $response, Spec\Response $responseSpec)
    {
        $actualBody = (string) $response->getBody();

        $actualBodyData = json_decode($actualBody, true);

        if ($actualBodyData !== $responseSpec->getBody()) {
            $message = sprintf("\t\t<error>JSON in body of the response is invalid, actual:</error>\n%s\n\t\t<error>Expected:</error>\n%s",
                $this->getOutput()->formatArray($actualBodyData, 3),
                $this->getOutput()->formatArray($responseSpec->getBody(), 3)
            );
            $this->addViolation($message);
        }
    }
}
