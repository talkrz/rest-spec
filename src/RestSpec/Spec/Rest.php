<?php

namespace RestSpec\Spec;

class Rest
{
    /**
     * List of all APIs sepcifications
     *
     * @var array
     */
    private $apiSpecs;

    /**
     * Current API specification
     *
     * @var Api
     */
    public $currentApiSpec;

    /**
     * @return array
     */
    public function getApiSpecs()
    {
        return $this->apiSpecs;
    }

    /**
     * Start with new API specification
     *
     * @param $baseUrl
     * @return Api
     */
    public function apiSpec($baseUrl)
    {
        if (!isset($this->apiSpecs[$baseUrl])) {
            $spec = new Api($baseUrl);
            $this->apiSpecs[$baseUrl] = $spec;
        }

        $this->currentApiSpec = $spec;

        return $spec;
    }

    /**
     * The singleton stuff
     *
     * @return static
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    protected function __construct() {}

    private function __clone() {}
}
