<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\LowerThan;

class LowerThanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult)
    {
        $expander = new LowerThan($boundary);
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return array(
            array(10.5, 10, true),
            array(-10.5, -20, true),
            array(1, 10, false),
            array(1, 1, false),
        );
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new LowerThan($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return array(
            array(1, "ipsum lorem", "Value \"ipsum lorem\" is not a valid number."),
            array(5, 10, "Value \"10\" is not lower than \"5\"."),
            array(5, 5, "Value \"5\" is not lower than \"5\"."),
        );
    }
}
