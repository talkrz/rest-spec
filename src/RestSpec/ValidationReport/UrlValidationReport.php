<?php

namespace RestSpec\ValidationReport;

use RestSpec\Validator\HasConsoleOutput;
use RestSpec\Spec;
use RestSpec\Output\ConsoleOutput;

class UrlValidationReport
{
    use HasConsoleOutput;

    /**
     * @var Spec\Url
     */
    private $spec;

    private $useCaseReports = [];


    public function __construct(Spec\Url $spec, ConsoleOutput $output)
    {
        $this->spec = $spec;
        $this->output = $output;
    }

    public function addUseCaseReport(UseCaseValidationReport $report)
    {
        $this->useCaseReports[] = $report;
    }

    public function getUseCaseReports()
    {
        return $this->useCaseReports;
    }

    public function dumpAsConsoleText()
    {
        $output = $this->getOutput()->getOutput();
        $output->writeln(sprintf(
            "<comment>%s</comment>\n\n<info>%s</info>\n",
            $this->spec->getDescription(),
            $this->spec->getUrl()
        ));

        foreach ($this->getUseCaseReports() as $report) {
            $report->dumpAsConsoleText();
        }
    }
}
