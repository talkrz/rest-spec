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

    public function validate(Response $response, Spec\Response $responseSpec)
    {
        $output = $this->getOutput()->getOutput();

        // first validate whether it is really a JSON
        $actualBodyData = json_decode((string) $response->getBody(), true);

        if ($actualBodyData !== null) {
            $output->writeln("\t\tResponse body is valid JSON");
        } else {
            $message = sprintf(
                "\t\t<error>Response body is not a valid JSON. Actual response body is:</error>\n<info>%s</info>",
                \RestSpec\Output\indentValue((string) $response->getBody(), 3)
            );
            $output->writeln($message);
            $this->addViolation($message);
        }

        // then if exact body specified check it matches spec
        if ($responseSpec->getBody()) {
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
        } elseif($constraint = $responseSpec->getBodyConstraint()) {
            $actualBody = (string) $response->getBody();

            $actualBodyData = json_decode($actualBody, true);

            $validator = Validation::createValidator();

            $violations = $validator->validateValue($actualBodyData, $constraint);

            if ($violations->count()) {

                $violationsDescription = '';
                foreach($violations as $violation) {
                    $violationsDescription .= $violation . PHP_EOL;
                }

                $message = sprintf(
                    "\t\t<error>Response body violates constraint:</error>\n<error>%s</error>",
                    \RestSpec\Output\indentValue($violationsDescription, 3)
                );

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
}
