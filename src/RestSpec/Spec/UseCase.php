<?php

namespace RestSpec\Spec;

use GuzzleHttp\Message\Request as GuzzleRequest;

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
    private $requestSpec;

    /**
     * Expected response specification
     *
     * @var Response
     */
    private $expectedResponseSpec;

    /**
     * Callback executed before testing use case
     * @var callable
     */
    private $beforeCallback;

    /**
     * Callback executed after testing use case is done
     * @var callable
     */
    private $doneCallback;

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
     * Create new request specification and return it for further modifications
     *
     * @return $this
     */
    public function givenRequest()
    {
        $this->requestSpec = new Request($this->baseUrl . $this->url);

        return $this->requestSpec;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        $request = $this->requestSpec->buildGuzzleRequest();

        if ($this->isATemplate() && $this->getExampleParameters()) {
            foreach($this->getExampleParameters() as $name => $value) {
                $actualValue = is_callable($value) ? $value() : $value;
                $this->replaceParameterInUrl($name, $actualValue, $request);
            }
        }
        return $request;
    }

    public function before(callable $before)
    {
        $this->beforeCallback = $before;
    }

    /**
     * @return callable
     */
    public function getBeforeCallback()
    {
        return $this->beforeCallback;
    }

    public function done(callable $done)
    {
        $this->doneCallback = $done;
    }

    /**
     * @return callable
     */
    public function getDoneCallback()
    {
        return $this->doneCallback;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getExampleUrl()
    {
        return $this->exampleUrl;
    }

    /**
     * Builds placeholder for example parameter for URL like {id}
     * @param  string $name
     * @return string
     */
    public function buildParameterPlaceholder($name)
    {
        return self::PARAMETER_LEFT_DELIMITER .
            $name .
            self::PARAMETER_RIGHT_DELIMITER;
    }

    private function replaceParameterInUrl($name, $value, GuzzleRequest $request)
    {
        $placeholder = $this->buildParameterPlaceholder($name);

        $this->exampleUrl = $this->exampleUrl ? $this->exampleUrl : $this->url;
        $this->exampleUrl = str_replace($placeholder, $value, $this->exampleUrl);
        $request->setUrl($this->baseUrl . $this->exampleUrl);
        if ($query = $this->requestSpec->getQuery()) {
            $request->setQuery($query);
        }
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
