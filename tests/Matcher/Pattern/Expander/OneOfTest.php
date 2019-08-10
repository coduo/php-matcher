<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\Contains;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\OneOf;
use PHPUnit\Framework\TestCase;

class OneOfTest extends TestCase
{
    public function test_not_enough_arguments()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('OneOf expander require at least two expanders.');

        $expander = new OneOf();
    }

    public function test_invalid_argument_types()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('OneOf expander require each argument to be a valid PatternExpander.');

        $expander = new OneOf('arg1', ['test']);
    }

    public function test_positive_match()
    {
        $expander = new OneOf(
            new Contains('lorem'),
            new Contains('test')
        );

        $this->assertTrue($expander->match('lorem ipsum'));
    }

    public function test_negative_match()
    {
        $expander = new OneOf(
            new Contains('lorem'),
            new Contains('test')
        );

        $this->assertFalse($expander->match('this is random stiring'));
        $this->assertSame(
            'Any expander available in OneOf expander does not match "this is random stiring".',
            $expander->getError()
        );
    }
}
