<?php

namespace RestSpec\Console\SpecView;

use RestSpec\Spec;

class UseCaseView
{
    /**
     * Display human readable info about URL use case
     *
     * @param  Spec\UseCase    $useCase an URL use case specification
     * @param  OutputInterface
     * @return string
     */
    public function view(Spec\UseCase $useCase)
    {
        $request = $useCase->getRequest();

        $output = sprintf("\t<options=bold>%s</options=bold>\n\n", $useCase->getDescription());


        if ($useCase->isATemplate()) {
            $url = $useCase->getExampleUrl();
        } else {
            $url = $useCase->getUrl();
        }

        if ($queryParameters = (string) $request->getQuery()) {
            $url .= '?' . urldecode($queryParameters);
        }

        $output .= sprintf("\t<info>%s %s</info>\n\n", $request->getMethod(), $url);

        if ($headers = $request->getHeaders()) {
            foreach ($headers as $headerName => $headerValue) {
                $output .= sprintf("\t%s: <info>%s</info>\n", $headerName, join('; ', $headerValue));
            }

            $output .= "\n";
        }

        if ($body = (string) $request->getBody()) {
            $json = json_decode($body);

            if ($json) {
                $bodyStr = json_encode($json, JSON_PRETTY_PRINT);
            } else {
                $bodyStr = $json;
            }

            $output .= sprintf("<info>%s</info>\n\n\n", \RestSpec\Output\indentValue($bodyStr, 1));
        }

        $output .= "\n";

        return $output;
    }
}
