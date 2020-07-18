<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\Contains;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\OneOf;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class OneOfTest extends TestCase
{
    public function test_not_enough_arguments()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('OneOf expander require at least two expanders.');

        $expander = new OneOf();
    }

    public function test_invalid_argument_types()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('OneOf expander require each argument to be a valid PatternExpander.');

        $expander = new OneOf('arg1', ['test']);
    }

    public function test_positive_match()
    {
        $backtrace = new Backtrace\InMemoryBacktrace();
        $contains = new Contains('lorem');
        $contains->setBacktrace($backtrace);
        $contains1 = new Contains('test');
        $contains1->setBacktrace($backtrace);

        $expander = new OneOf(
            $contains,
            $contains1
        );
        $expander->setBacktrace(new Backtrace\InMemoryBacktrace());

        $this->assertTrue($expander->match('lorem ipsum'));
    }

    public function test_negative_match()
    {
        $backtrace = new Backtrace\InMemoryBacktrace();
        $contains = new Contains('lorem');
        $contains->setBacktrace($backtrace);
        $contains1 = new Contains('test');
        $contains1->setBacktrace($backtrace);

        $expander = new OneOf(
            $contains,
            $contains1
        );
        $expander->setBacktrace($backtrace);

        $this->assertFalse($expander->match('this is random stiring'));
        $this->assertSame(
            'Any expander available in OneOf expander does not match "this is random stiring".',
            $expander->getError()
        );
    }
}
