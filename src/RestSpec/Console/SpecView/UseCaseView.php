<?php

namespace RestSpec\Console\SpecView;

use RestSpec\Spec,
    Symfony\Component\Console\Output\OutputInterface;

class UseCaseView
{
    /**
     * Display human readable info about URL use case
     *
     * @param  Spec\UseCase    $useCase an URL use case specification
     * @param  OutputInterface
     * @return void
     */
    public function view(Spec\UseCase $useCase, OutputInterface $output)
    {
        $request = $useCase->getRequest();

        $output->writeln(sprintf("\t<options=bold>%s</options=bold>\n", $useCase->getDescription()));


        if ($useCase->isATemplate()) {
            $url = $useCase->getExampleUrl();
        } else {
            $url = $useCase->getUrl();
        }

        if ($queryParameters = (string) $request->getQuery()) {
            $url .= '?' . urldecode($queryParameters);
        }

        $output->writeln(sprintf("\t<info>%s %s</info>\n", $request->getMethod(), $url));

        if ($headers = $request->getHeaders()) {

            foreach($headers as $headerName => $headerValue) {
                $output->writeln(sprintf("\t%s: <info>%s</info>", $headerName, join('; ', $headerValue)));
            }

            $output->writeln('');
        }

        if ($body = (string) $request->getBody()) {
            $json = json_decode($body);

            if ($json) {
                $bodyStr = json_encode($json, JSON_PRETTY_PRINT);
            } else {
                $bodyStr = $json;
            }

            $output->writeln(sprintf("<info>%s</info>\n\n", \RestSpec\Output\indentValue($bodyStr, 1)));
        }

        $output->write(PHP_EOL);
    }
}
