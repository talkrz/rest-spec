<?php

namespace RestSpec\Spec;

use Symfony\Component\Validator\Constraint;

class Response
{
    /**
     * Body types
     */
    const BODY_TYPE_PLAIN = 1;
    const BODY_TYPE_JSON = 2;

    /**
     * HTTP status code of the response
     * @var int
     */
    private $statusCode;

    /**
     * Type of the response pody (plain text/JSON etc.)
     * @var int
     */
    private $bodyType;

    /**
     * Content of the responsee body
     * @var string
     */
    private $body;

    /**
     * Constraint response body have to match
     *
     * @var Constraint
     */
    private $bodyConstraint;

    /**
     * Required headers of the response
     *
     * @var array
     */
    private $requiredHeaders;

    /**
     * Define expected status code
     *
     * @param  int $code HTTP status code
     * @return Response
     */
    public function toHaveStatusCode($code)
    {
        $this->statusCode = $code;

        return $this;
    }

    /**
     * Define expected single header
     *
     * @param  string $name  header's name
     * @param  string $value header's value
     * @return Response
     */
    public function toHaveHeader($name, $value)
    {
        $this->requiredHeaders[$name] = $value;

        return $this;
    }

    /**
     * Define expected multiple headers
     *
     * @param  array  $headers array of expected headers
     * @return Response
     */
    public function toHaveHeaders(array $headers)
    {
        foreach($headers as $headerName => $headerValue) {
            $this->toHaveHeader($headerName, $headerValue);
        }

        return $this;
    }

    /**
     * Response contains body in JSON format
     *
     * @return Response
     */
    public function toBeJson()
    {
        return $this->toHaveBodyType(self::BODY_TYPE_JSON);
    }

    /**
     * Define expected body type
     *
     * @param  int $bodyType see BODY_TYPE_* consts
     * @return Response
     */
    public function toHaveBodyType($bodyType)
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * Set expected constraints body should match
     *
     * @param  \Closure $constraintDefinition a closure returning Symfony Validation constraint
     * @return Response
     */
    public function toHaveBodyThatFollows(\Closure $constraintDefinition)
    {
        $this->bodyConstraint = $constraintDefinition();

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return mixed
     */
    public function getRequiredHeaders()
    {
        return $this->requiredHeaders;
    }

    /**
     * @return Constraint
     */
    public function getBodyConstraint()
    {
        return $this->bodyConstraint;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getBodyType()
    {
        return $this->bodyType;
    }
}
