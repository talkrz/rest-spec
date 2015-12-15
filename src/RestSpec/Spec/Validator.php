<?php

namespace RestSpec\Spec;

class Validator
{
    /**
     * First pass of the spec: parse and stop execution on errors
     * @todo: introduce ValidationException
     *
     * @param  SpecRest $restSpec
     * @return void
     */
    public function validate(Rest $restSpec)
    {
        $apiSpec = $restSpec->getApiSpecs();

        if (empty($apiSpec)) {
            throw new \RuntimeException('No API spec provided');
        }

        foreach($apiSpec as $apiSpec) {
            foreach($apiSpec->getUrlSpecs() as $urlSpec) {
                $useCases = $urlSpec->getUseCases();

                if (!$useCases) {
                    throw new \RuntimeException('You have to specify use cases inside the URL specificetion');
                }

                foreach($useCases as $urlUseCaseSpec) {
                    if (!$urlUseCaseSpec->getRequest()) {
                        throw new \RuntimeException('You have to add request specification using givenRequest() function');
                    }

                    if (!$urlUseCaseSpec->getExpectedResponseSpec()) {
                        throw new \RuntimeException('You have to specify expected response using expectResponse() function');
                    }

                    if ($urlUseCaseSpec->isATemplate() && !$urlUseCaseSpec->getExampleParameters()) {
                        throw new \RuntimeException('To use an URL template you have to provide example parameters to call the URL with.');
                    }

                    if ($exampleParams = $urlUseCaseSpec->getExampleParameters()) {
                        foreach($exampleParams as $name => $value) {
                            $placeholder = $urlUseCaseSpec->buildParameterPlaceholder($name);

                            if (strpos($urlUseCaseSpec->getUrl(), $placeholder) === false) {
                                throw new \RuntimeException(sprintf('You specified example parameter, but the placeholder "%s" for it is missing in your URL', $placeholder));
                            }
                        }
                    }
                }
            }
        }
    }
}
