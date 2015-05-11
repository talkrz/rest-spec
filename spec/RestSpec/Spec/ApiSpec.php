<?php

namespace spec\RestSpec\Spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ApiSpec extends ObjectBehavior
{
    private $baseUrl = 'http://example.com';

    function it_is_initializable()
    {
        $this->beConstructedWith($this->baseUrl);
        $this->shouldHaveType('RestSpec\Spec\Api');
    }

    function it_is_initialized_with_base_url()
    {
        $this->beConstructedWith($this->baseUrl);
        $this->getBaseUrl()->shouldReturn($this->baseUrl);
    }
}
