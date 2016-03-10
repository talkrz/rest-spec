<?php

namespace spec\RestSpec\ValidationReport;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ValidationReportSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RestSpec\ValidationReport\ValidationReport');
    }

    public function it_starts_with_use_case_counters_equals_zero()
    {
        $this->getUseCasesPassedCount()->shouldReturn(0);
        $this->getUseCasesFailedCount()->shouldReturn(0);
    }

    public function it_should_increment_passed_use_cases_counter()
    {
        $this->incrementPassedCount();
        $this->getUseCasesPassedCount()->shouldReturn(1);
    }

    public function it_should_increment_failed_use_cases_counter()
    {
        $this->incrementFailedCount();
        $this->getUseCasesFailedCount()->shouldReturn(1);
    }
}
