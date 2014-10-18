<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\Contains;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\OneOf;

class OneOfTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage OneOf expander require at least two expanders.
     */
    public function test_not_enough_arguments()
    {
        $expander = new OneOf();
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage OneOf expander require each argument to be a valid PatternExpander.
     */
    public function test_invalid_argument_types()
    {
        $expander = new OneOf("arg1", array("test"));
    }

    public function test_positive_match()
    {
        $expander = new OneOf(
            new Contains("lorem"),
            new Contains("test")
        );

        $this->assertTrue($expander->match("lorem ipsum"));
    }

    public function test_negative_match()
    {
        $expander = new OneOf(
            new Contains("lorem"),
            new Contains("test")
        );

        $this->assertFalse($expander->match("this is random stiring"));
        $this->assertSame(
            "Any expander available in OneOf expander does not match \"this is random stiring\".",
            $expander->getError()
        );
    }
}
