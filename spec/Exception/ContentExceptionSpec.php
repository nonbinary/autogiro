<?php

declare(strict_types = 1);

namespace spec\byrokrat\autogiro\Exception;

use byrokrat\autogiro\Exception\ContentException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContentExceptionSpec extends ObjectBehavior
{
    const ERRORS = ['foo', 'bar'];

    function let()
    {
        $this->beConstructedWith(self::ERRORS);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ContentException::CLASS);
    }

    function it_is_throwable()
    {
        $this->shouldHaveType(\Throwable::CLASS);
    }

    function it_contains_error_messages()
    {
        $this->getErrors()->shouldEqual(self::ERRORS);
    }

    function it_contains_an_exception_message()
    {
        $this->__tostring()->shouldMatch('/foo/');
        $this->__tostring()->shouldMatch('/bar/');
    }
}
