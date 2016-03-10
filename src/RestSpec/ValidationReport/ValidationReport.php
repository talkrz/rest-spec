<?php

namespace RestSpec\ValidationReport;

use RestSpec\Validator\HasConsoleOutput;

class ValidationReport
{
    use HasConsoleOutput;

    private $useCasesPassedCount = 0;

    private $useCasesFailedCount = 0;

    private $apiReports = [];

    public function addApiReport(ApiValidationReport $report)
    {
        $this->apiReports[] = $report;
    }

    public function getApiReports()
    {
        return $this->apiReports;
    }

    public function getUseCasesPassedCount()
    {
        return $this->useCasesPassedCount;
    }

    public function getUseCasesFailedCount()
    {
        return $this->useCasesFailedCount;
    }

    public function getTotalUseCases()
    {
        return $this->getUseCasesPassedCount() + $this->getUseCasesFailedCount();
    }

    public function incrementPassedCount()
    {
        ++$this->useCasesPassedCount;
    }

    public function incrementFailedCount()
    {
        ++$this->useCasesFailedCount;
    }

    public function dumpAsConsoleText($apiFilter, $useCaseFilter)
    {
        $output = $this->getOutput()->getOutput();

        foreach ($this->getApiReports() as $apiReport) {
            $apiReport->dumpAsConsoleText();
        }

        $totalUseCases = $this->getTotalUseCases();

        if ($totalUseCases) {
            $output->write(sprintf(
                'Tested %d use cases. (<info>Passed: %d</info>',
                $totalUseCases,
                $this->getUseCasesPassedCount()
            ));
            if ($this->getUseCasesFailedCount() > 0) {
                $output->writeln(sprintf(', <error>Failed: %d</error>)', $this->getUseCasesFailedCount()));
            } else {
                $output->writeln(')');
            }
        } else {
            $output->writeln('No use cases matching your criteria:');
            $output->writeln(sprintf('  - api filter: %s', $apiFilter ? $apiFilter : '[none]'));
            $output->writeln(sprintf('  - use case filter: %s', $useCaseFilter ? $useCaseFilter : '[none]'));
        }
    }
}
