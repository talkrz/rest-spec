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

    public function hasStatusCode($code)
    {
        $this->statusCode = $code;

        return $this;
    }

    public function hasHeader($name, $value)
    {
        $this->requiredHeaders[$name] = $value;

        return $this;
    }

    public function hasHeaders(array $headers)
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

    public function hasBodyType($bodyType)
    {
        $this->bodyType = $bodyType;

        return $this;
    }

    /**
     * Response contains body in JSON format
     *
     * @return Response
     */
    public function toBeJson()
    {
        return $this->hasBodyType(self::BODY_TYPE_JSON);
    }

    public function hasBodyEquals($expectedBody)
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
