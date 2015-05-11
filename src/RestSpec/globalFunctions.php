<?php

/**
 * Define an API specification as a set of URLs specifications
 *
 * @param $baseUrl
 * @param callable $urlSpecs
 */
function api($baseUrl, callable $urlSpecs)
{
    RestSpec\api($baseUrl, $urlSpecs);
}

/**
 * Define an URL as a set of use cases specifications
 * @param $url
 * @param $description
 * @param callable $useCasesSpecs
 */
function url($url, $description, callable $useCasesSpecs)
{
    RestSpec\url($url, $description, $useCasesSpecs);
}

/**
 * Define an URL use case
 *
 * @param $description
 * @return mixed
 */
function useCase($description, callable $useCaseDefinition)
{
    RestSpec\useCase($description, $useCaseDefinition);
}

/**
 * Define a request
 *
 * @param callable $requestDefinition
 */
function givenRequest(callable $requestDefinition)
{
    RestSpec\givenRequest($requestDefinition);
}

/**
 * Define expected response
 *
 * @return RestSpec\Spec\Response
 */
function expectResponse()
{
    return RestSpec\expectResponse();
}
