<?php

namespace RestSpec\ValidationReport;

use RestSpec\Spec;

class UrlValidationReport
{
    /**
     * @var Spec\Url
     */
    private $spec;

    /**
     * @var UseCaseValidationReport[]
     */
    private $useCaseReports = [];

    /**
     * @param Spec\Url $spec [description]
     */
    public function __construct(Spec\Url $spec)
    {
        $this->spec = $spec;
    }

    /**
     * @param UseCaseValidationReport $report
     */
    public function addUseCaseReport(UseCaseValidationReport $report)
    {
        $this->useCaseReports[] = $report;
    }

    /**
     * @return UseCaseValidationReport
     */
    public function getUseCaseReports()
    {
        return $this->useCaseReports;
    }

    /**
     * @return string
     */
    public function dumpAsConsoleText()
    {
        $output = sprintf(
            "<comment>%s</comment>\n\n<info>%s</info>\n\n",
            $this->spec->getDescription(),
            $this->spec->getUrl()
        );

        foreach ($this->getUseCaseReports() as $report) {
            $output .= $report->dumpAsConsoleText();
        }

        return $output;
    }
}
