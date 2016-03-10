<?php

namespace RestSpec\ValidationReport;

use RestSpec\Spec;

class ApiValidationReport
{
    /**
     * @var Spec\Api
     */
    private $spec;

    /**
     * @var UrlValidationReport[]
     */
    private $urlReports = [];

    /**
     * @param Spec\Api $spec
     */
    public function __construct(Spec\Api $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @param UrlValidationReport $report
     */
    public function addUrlReport(UrlValidationReport $report)
    {
        $this->urlReports[] = $report;
    }

    /**
     * @return UrlValidationReport[]
     */
    public function getUrlReports()
    {
        return $this->urlReports;
    }

    /**
     * @return string
     */
    public function dumpAsConsoleText()
    {
        $output = sprintf("\nAPI base URL: <info>%s</info>\n\n", $this->spec->getBaseUrl());

        foreach ($this->getUrlReports() as $report) {
            $output .= $report->dumpAsConsoleText();
        }

        return $output;
    }
}
