<?php

namespace spec\RestSpec\Spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Validator\Constraints as Assert;

class ResponseSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RestSpec\Spec\Response');
    }

    public function it_allows_to_define_expected_response_status_code()
    {
        $this->toHaveStatusCode(200)->shouldReturn($this);
        $this->getStatusCode()->shouldReturn(200);
    }

    public function it_allows_to_define_expected_single_header()
    {
        $this->toHaveHeader('Content-Type', 'application/json')->shouldReturn($this);
        $this->getRequiredHeaders()->shouldReturn([
            'Content-Type' => 'application/json',
        ]);
    }

    public function it_allows_to_define_expected_multiple_headers()
    {
        $expectedHeaders = [
            'Content-Type' => 'application/json',
            'X-Some-Header' => 'some-header-value',
        ];

        $this->toHaveHeaders($expectedHeaders)->shouldReturn($this);
        $this->getRequiredHeaders()->shouldReturn($expectedHeaders);
    }

    public function it_allows_to_specify_that_response_is_a_json()
    {
        $this->toBeJson()->shouldReturn($this);
        $this->getBodyType()->shouldReturn(\RestSpec\Spec\Response::BODY_TYPE_JSON);
    }

    public function it_allows_to_define_expected_body_type()
    {
        $this->toHaveBodyType(\RestSpec\Spec\Response::BODY_TYPE_JSON)->shouldReturn($this);
        $this->getBodyType()->shouldReturn(\RestSpec\Spec\Response::BODY_TYPE_JSON);
    }

    public function it_allows_to_define_validation_constraints_for_body()
    {
        $assert = new Assert\NotBlank();
        $constraints = function () use ($assert) {
            return $assert;
        };

        $this->toHaveBodyThatFollows($constraints)->shouldReturn($this);
        $this->getBodyConstraint()->shouldReturn($assert);
    }
}
