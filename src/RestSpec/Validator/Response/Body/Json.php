<?php

namespace RestSpec\Validator\Response\Body;

use GuzzleHttp\Message\Response;
use RestSpec\Spec;
use RestSpec\Validator\HasConsoleOutput;
use RestSpec\Validator\Validator;
use Symfony\Component\Validator\Validation;

class Json extends Validator
{
    use HasConsoleOutput;

    const JSON_OUTPUT_MAX_LENGTH = 20000;

    public function validate(Response $response, Spec\Response $responseSpec)
    {
        $output = $this->getOutput()->getOutput();

        // first validate whether it is really a JSON
        $actualBodyData = json_decode((string) $response->getBody(), true);

        $output->writeln('');

        if ($actualBodyData !== null) {
            $output->writeln("\t\tResponse body is valid JSON");
        } else {
            $message = sprintf(
                "Response body is not a valid JSON. Actual response body is:\n\n%s",
                strip_tags((string) $response->getBody())
            );

            $message = \RestSpec\Output\textBox($message, function($line) {
                return '<error>' . $line . '</error>';
            }, 3);

            $output->writeln($message);
            $this->addViolation($message);
        }

        // then if exact body specified check it matches spec
        if ($responseSpec->getBody()) {
            $this->validateBody($response, $responseSpec);
        } elseif($constraint = $responseSpec->getBodyConstraint()) {
            $actualBody = (string) $response->getBody();

            $actualBodyData = json_decode($actualBody, true);

            $validator = Validation::createValidator();

            try {
                $violations = $validator->validateValue($actualBodyData, $constraint);
            } catch(\Symfony\Component\Validator\Exception\UnexpectedTypeException $e) {
                throw new \RuntimeException(
                    sprintf(
                        "The type of body response is invalid, actual value: %s\n\n(common mistake: you expect a collection in the response but get a single result)\n", $actualBody),
                    0,
                    $e
                );
            }

            if ($violations->count()) {

                $violationsDescription = '';
                foreach($violations as $violation) {
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

                $message = \RestSpec\Output\textBox($message, function($line) {
                    return '<error>' . $line . '</error>';
                }, 3);

                $output->writeln($message);
                $this->addViolation($message);
            } else {
                $output->writeln(
                    sprintf(
                        "\t\tResponse body matches required constraint:\n%s",
                        \RestSpec\Output\indentValue(
                            $this->getOutput()->getConstraintDescriber()->describe($constraint), 3
                        )
                    )
                );
            }
        }

        return $this->isValid();
    }

    public function validateBody(Response $response, Spec\Response $responseSpec)
    {
        $output = $this->getOutput()->getOutput();

        $actualBody = (string) $response->getBody();

        $actualBodyData = json_decode($actualBody, true);

        if ($actualBodyData === $responseSpec->getBody()) {
            $output->writeln(
                sprintf(
                    "\t\tResponse body is valid JSON:\n<info>%s</info>",
                    $this->getOutput()->formatArray($actualBodyData, 3)
                )
            );
        } else {
            $message = sprintf("\t\t<error>JSON in body of the response is invalid, actual:</error>\n%s\n\t\t<error>Expected:</error>\n%s",
                $this->getOutput()->formatArray($actualBodyData, 3),
                $this->getOutput()->formatArray($responseSpec->getBody(), 3)
            );
            $output->writeln($message);
            $this->addViolation($message);
        }
    }
}
