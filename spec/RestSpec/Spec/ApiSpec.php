<?php

namespace spec\RestSpec\Spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiSpec extends ObjectBehavior
{
    private $baseUrl = 'http://example.com';

    public function it_is_initializable()
    {
        $this->beConstructedWith($this->baseUrl, 'Some name');
        $this->shouldHaveType('RestSpec\Spec\Api');
    }

    public function it_is_initialized_with_base_url()
    {
        $this->beConstructedWith($this->baseUrl, 'Some name');
        $this->getBaseUrl()->shouldReturn($this->baseUrl);
        $this->getName()->shouldReturn('Some name');
    }
}
