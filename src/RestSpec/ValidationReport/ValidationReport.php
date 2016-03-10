<?php

namespace RestSpec\ValidationReport;

class ValidationReport
{
    /**
     * @var integer
     */
    private $useCasesPassedCount = 0;

    /**
     * @var integer
     */
    private $useCasesFailedCount = 0;

    /**
     * @var ApiValidationReport[]
     */
    private $apiReports = [];

    /**
     * @param ApiValidationReport $report
     */
    public function addApiReport(ApiValidationReport $report)
    {
        $this->apiReports[] = $report;
    }

    /**
     * @return ApiValidationReport[]
     */
    public function getApiReports()
    {
        return $this->apiReports;
    }

    /**
     * @return integer
     */
    public function getUseCasesPassedCount()
    {
        return $this->useCasesPassedCount;
    }

    /**
     * @return integer
     */
    public function getUseCasesFailedCount()
    {
        return $this->useCasesFailedCount;
    }

    /**
     * @return integer
     */
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
        $output = '';

        foreach ($this->getApiReports() as $apiReport) {
            $output .= $apiReport->dumpAsConsoleText();
        }

        $totalUseCases = $this->getTotalUseCases();

        if ($totalUseCases) {
            $output .= sprintf(
                'Tested %d use cases. (<info>Passed: %d</info>',
                $totalUseCases,
                $this->getUseCasesPassedCount()
            );
            if ($this->getUseCasesFailedCount() > 0) {
                $output .= sprintf(", <error>Failed: %d</error>)\n", $this->getUseCasesFailedCount());
            } else {
                $output .= ")\n";
            }
        } else {
            $output .= "No use cases matching your criteria:\n";
            $output .= sprintf("  - api filter: %s\n", $apiFilter ? $apiFilter : '[none]');
            $output .= sprintf("  - use case filter: %s\n", $useCaseFilter ? $useCaseFilter : '[none]');
        }

        return $output;
    }
}
