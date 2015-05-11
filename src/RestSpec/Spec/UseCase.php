<?php

namespace RestSpec\Spec;

use GuzzleHttp\Message\Request;

class UseCase
{
    /**
     * Human readable description of the use case
     *
     * @var string
     */
    private $description;

    /**
     * Definition of the request to be performed
     *
     * @var Request
     */
    private $request;

    /**
     * Expected response specification
     *
     * @var Response
     */
    private $expectedResponseSpec;

    /**
     * The base URL
     *
     * @var string
     */
    private $baseUrl;

    /**
     * An URL to be requested
     *
     * @var string
     */
    private $url;

    /**
     * @param $baseUrl
     * @param $url
     */
    public function __construct($baseUrl, $url)
    {
        $this->baseUrl = $baseUrl;
        $this->url = $url;
    }

    /**
     * @return Response
     */
    public function getExpectedResponseSpec()
    {
        return $this->expectedResponseSpec;
    }

    /**
     * @param callable $requestDefinition
     * @return $this
     */
    public function givenRequest(\Closure $requestDefinition)
    {
        $request = new \GuzzleHttp\Message\Request('GET', $this->baseUrl . $this->url);

        $requestDefinition($request);

        $this->request = $request;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param ResponseSpec $responseSpec
     * @return Response
     */
    public function expectResponse(ResponseSpec $responseSpec = null)
    {
        if ($responseSpec) {
            $this->expectedResponseSpec = $responseSpec;
        } else {
            $this->expectedResponseSpec = new Response();

            return $this->expectedResponseSpec;
        }
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param strnig $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
