<?php

namespace RestSpec\Spec;

use Doctrine\Common\Collections\ArrayCollection;

class Api
{
    /**
     * The base URL of the API
     *
     * @var string
     */
    private $baseUrl;

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

    /**
     * @var ArrayCollection
     */
    private $dataFixtures;

    public function __construct($baseUrl)
    {
        $this->baseUrl = $baseUrl;
        $this->dataFixtures = new ArrayCollection();
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
     * @return ArrayCollection
     */
    public function getDataFixtures()
    {
        return $this->dataFixtures;
    }

    /**
     * @return Url
     */
    public function getCurrentUrlSpec()
    {
        return $this->currentUrlSpec;
    }
}
