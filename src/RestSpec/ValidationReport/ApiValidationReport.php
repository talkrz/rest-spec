<?php

namespace RestSpec\ValidationReport;

use RestSpec\Validator\HasConsoleOutput;
use RestSpec\Spec;
use RestSpec\Output\ConsoleOutput;

class ApiValidationReport
{
    use HasConsoleOutput;

    /**
     * @var Spec\Api
     */
    private $spec;

    private $urlReports = [];

    public function __construct(Spec\Api $spec, ConsoleOutput $output)
    {
        $this->spec = $spec;
        $this->output = $output;
    }

    public function addUrlReport(UrlValidationReport $report)
    {
        $this->urlReports[] = $report;
    }

    public function getUrlReports()
    {
        return $this->urlReports;
    }

    public function dumpAsConsoleText()
    {
        $output = $this->getOutput()->getOutput();
        $output->writeln(sprintf("\nAPI base URL: <info>%s</info>\n", $this->spec->getBaseUrl()));

        foreach ($this->getUrlReports() as $report) {
            $report->dumpAsConsoleText();
        }
    }
}
