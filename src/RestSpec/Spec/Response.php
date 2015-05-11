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
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function toHasStatusCode($code)
    {
        $this->statusCode = $code;

        return $this;
    }

    public function toHasHeader($name, $value)
    {
        $this->requiredHeaders[$name] = $value;

        return $this;
    }

    public function toHasHeaders(array $headers)
    {
        foreach($headers as $headerName => $headerValue) {
            $this->toHasHeader($headerName, $headerValue);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequiredHeaders()
    {
        return $this->requiredHeaders;
    }

    public function toHasBodyType($bodyType)
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    public function toHasBodyEquals($expectedBody)
    {
        $this->body = $expectedBody;

        return $this;
    }

    public function bodyMatchesConstraint(\Closure $constraintDefinition)
    {
        $this->bodyConstraint = $constraintDefinition();
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
