<?php

namespace RestSpec\Spec;

use GuzzleHttp\Message\Request as GuzzleRequest;
use GuzzleHttp\Stream\Stream;

class Request
{
    private $url;

    private $method = 'GET';

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
     * Set request body
     *
     * @param  string
     * @return Request
     */
    public function body($body)
    {
        $this->body = $body;
        return $this;
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

        $request->setBody(Stream::factory($this->body));

        return $request;
    }
}