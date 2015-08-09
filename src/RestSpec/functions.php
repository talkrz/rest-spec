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

    return $useCaseSpec;
}

/**
 * Define a request
 */
function givenRequest()
{
    $restSpec = Spec\Rest::getInstance();

    $useCaseSpec = $restSpec->currentApiSpec->getCurrentUrlSpec()->getCurrentUseCaseSpec();

    return $useCaseSpec->givenRequest();
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

/**
 * Callback executed after testing use case is done
 *
 * @param  callable $done
 * @return void
 */
function done(callable $done)
{
    $restSpec = Spec\Rest::getInstance();

    $useCaseSpec = $restSpec->currentApiSpec->getCurrentUrlSpec()->getCurrentUseCaseSpec();

    return $useCaseSpec->done($done);
}

/**
 * Return data fixtures container
 *
 * @return \Doctrine\Common\Collections\ArrayCollection
 */
function dataFixtures()
{
    $restSpec = Spec\Rest::getInstance();

    $apiSpec = $restSpec->currentApiSpec;

    return $apiSpec->getDataFixtures();
}
