<?php

namespace RestSpec\Spec;

class Url
{
    /**
     * The base URL
     *
     * @var string
     */
    public $baseUrl;

    /**
     * URL
     *
     * @var string
     */
    private $url;

    /**
     * Description what this URL is responsible for
     * @var string
     */
    private $description;

    /**
     * A list of use cases for this URL
     *
     * @var array
     */
    private $useCases;

    /**
     * @var UseCase
     */
    private $currentUseCaseSpec;

    /**
     * Define new use case
     *
     * @param $description
     * @return UseCase
     */
    public function useCase($description)
    {
        $urlUseCaseSpec = new UseCase($this->baseUrl, $this->url);
        $urlUseCaseSpec->setDescription($description);

        $this->useCases[] = $urlUseCaseSpec;

        $this->currentUseCaseSpec = $urlUseCaseSpec;

        return $urlUseCaseSpec;
    }

    /**
     * @return UseCase
     */
    public function getCurrentUseCaseSpec()
    {
        return $this->currentUseCaseSpec;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return array
     */
    public function getUseCases()
    {
        return $this->useCases;
    }
}
