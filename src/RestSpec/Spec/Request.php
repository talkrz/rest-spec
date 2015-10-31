<?php

namespace RestSpec\Spec;

use GuzzleHttp\Message\Request as GuzzleRequest;
use GuzzleHttp\Stream\Stream;

class Request
{
    private $url;

    private $method = 'GET';

    private $query = [];

    private $headers = [];

    private $body = '';

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Set request method
     *
     * @param  string $method
     * @return Request
     */
    public function method($method)
    {
        $this->method = $method;
        return $this;
    }

    public function query(array $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * Get request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get query parameters
     *
     * @return array
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set headers
     *
     * @param  array   $headers list of headers, like ["Content-Type" => "application/json"]
     * @return Request
     */
    public function headers(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get list of headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set request body
     *
     * @param  string
     * @return Request
     */
    public function body($body)
    {
        if (!is_string($body)) {
            throw new \InvalidArgumentException('The request body should be a string.');
        }
        $this->body = $body;
        return $this;
    }

    /**
     * Get body
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Build Guzzle request object based on defined specification
     *
     * @return GuzzleRequest
     */
    public function buildGuzzleRequest()
    {
        $request = new GuzzleRequest($this->method, $this->url);

        if (!empty($this->headers)) {
            $request->setHeaders($this->headers);
        }

        if (!empty($this->query)) {
            $request->setQuery($this->query);
        }

        $request->setBody(Stream::factory($this->body));

        return $request;
    }
}
