<?php

namespace RestSpec;

// @todo: there should be way to avoid require global functions if someone does not want them
// @todo: but for now they are always required
require_once __DIR__ . '/globalFunctions.php';

/**
 * Define an API specification as a set of URLs specifications
 *
 * @param $baseUrl
 * @param callable $urlSpecs
 */
function api($baseUrl, callable $urlSpecs)
{
    $restSpec = Spec\Rest::getInstance();
    $restSpec->apiSpec($baseUrl);

    $urlSpecs();
}

/**
 * Define an URL as a set of use cases specifications
 * @param $url
 * @param $description
 * @param callable $useCasesSpecs
 */
function url($url, $description, callable $useCasesSpecs)
{
    $restSpec = Spec\Rest::getInstance();

    $apiSpec = $restSpec->currentApiSpec;

    $urlSpec = $apiSpec->urlSpec($url);
    $urlSpec->setDescription($description);

    $useCasesSpecs();
}

/**
 * Define a use case
 *
 * @param $description
 * @return mixed
 */
function useCase($description, callable $useCaseDefinition)
{
    $restSpec = Spec\Rest::getInstance();

    $urlSpec = $restSpec->currentApiSpec->getCurrentUrlSpec();

    $useCaseSpec = $urlSpec->useCase($description);

    $useCaseDefinition();
}

/**
 * Define a request
 *
 * @param callable $requestDefinition
 */
function givenRequest(callable $requestDefinition)
{
    $restSpec = Spec\Rest::getInstance();

    $useCaseSpec = $restSpec->currentApiSpec->getCurrentUrlSpec()->getCurrentUseCaseSpec();

    $useCaseSpec->givenRequest($requestDefinition);
}

/**
 * Define expected response
 *
 * @return Spec\Response
 */
function expectResponse()
{
    $restSpec = Spec\Rest::getInstance();

    $useCaseSpec = $restSpec->currentApiSpec->getCurrentUrlSpec()->getCurrentUseCaseSpec();

    return $useCaseSpec->expectResponse();
}
