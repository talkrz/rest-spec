<?php

namespace RestSpec\Spec;

class Api
{
    /**
     * The base URL of the API
     *
     * @var string
     */
    private $baseUrl;

    /**
     * The name of the API
     *
     * @var string
     */
    private $name;

    /**
     * Specifications for every available URL
     *
     * @var array
     */
    private $urlSpecs;

    /**
     * Current URL specification
     * @var Url
     */
    private $currentUrlSpec;

    public function __construct($baseUrl, $name)
    {
        $this->baseUrl = $baseUrl;
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getUrlSpecs()
    {
        return $this->urlSpecs;
    }

    /**
     * @param $url
     * @return Url
     */
    public function urlSpec($url)
    {
        if (!isset($this->urlSpecs[$url])) {
            $spec = new Url();
            $spec->baseUrl = $this->baseUrl;
            $spec->setUrl($url);
            $this->urlSpecs[$url] = $spec;
        }

        $this->currentUrlSpec = $spec;

        return $spec;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @param string $baseUrl
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return Url
     */
    public function getCurrentUrlSpec()
    {
        return $this->currentUrlSpec;
    }

    /**
     * Get API name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
