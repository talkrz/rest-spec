<?php

namespace spec\RestSpec\Spec;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RequestSpec extends ObjectBehavior
{
    function let(\RestSpec\Spec\Request $request)
    {
        $this->beConstructedWith('/foo');
    }

    function it_is_initializable_with_an_url()
    {
        $this->shouldHaveType('RestSpec\Spec\Request');
        $this->getUrl()->shouldBe('/foo');
    }

    function it_allows_to_set_request_method()
    {
        $this->method('POST')->shouldReturn($this);
        $this->getMethod()->shouldReturn('POST');
    }

    function it_allows_to_set_query_params()
    {
        $queryParams = [
            'foo' => 'bar',
        ];

        $this->query($queryParams)->shouldReturn($this);
        $this->getQuery()->shouldReturn($queryParams);
    }

    function it_allows_to_set_list_of_headers()
    {
        $headers = [
            'X-LOL-HEADER' => 'xD',
        ];

        $this->headers($headers)->shouldReturn($this);
        $this->getHeaders()->shouldReturn($headers);
    }

    function it_allows_to_specify_body()
    {
        $body = 'a body';

        $this->body($body)->shouldReturn($this);
        $this->getBody()->shouldReturn($body);
    }

    function it_does_not_allow_to_set_array_body()
    {
        $wrongBody = ['wrong body'];
        $this->shouldThrow('\InvalidArgumentException')->duringBody($wrongBody);
    }
}
