<?php

namespace RestSpec\Spec;

use GuzzleHttp\Message\Request;

class UseCase
{
    const PARAMETER_LEFT_DELIMITER = '{';
    const PARAMETER_RIGHT_DELIMITER = '}';

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

    private $exampleUrl;

    /**
     * @var array
     */
    private $exampleParameters;

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
        if ($this->isATemplate()) {
            if (!$this->getExampleParameters()) {
                throw new \RuntimeException('To use an URL template you have to provide example parameters to call the URL with.');
            }

            foreach($this->getExampleParameters() as $name => $value) {
                $this->replaceParameterInUrl($name, $value);
            }
        }
        return $this->request;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getExampleUrl()
    {
        return $this->exampleUrl;
    }

    public function replaceParameterInUrl($name, $value)
    {
        $placeholder =
            self::PARAMETER_LEFT_DELIMITER .
            $name .
            self::PARAMETER_RIGHT_DELIMITER;

        if (strpos($this->getUrl(), $placeholder) === false) {
            throw new \RuntimeException(sprintf('You should have %s placeholder for example parameter in your URL', $placeholder));
        }

        $this->exampleUrl = $this->url;
        $this->exampleUrl = str_replace($placeholder, $value, $this->exampleUrl);
        $this->request->setUrl($this->baseUrl . $this->exampleUrl);

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

    public function isATemplate()
    {
        return strpos($this->url, self::PARAMETER_LEFT_DELIMITER) !== false &&
            strpos($this->url, self::PARAMETER_RIGHT_DELIMITER) !== false;
    }

    public function withExampleParameters(array $parameters)
    {
        $this->exampleParameters = $parameters;
    }

    public function getExampleParameters()
    {
        return $this->exampleParameters;
    }
}
