<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\GreaterThan;

class GreaterThanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_examples($boundary, $value, $expectedResult)
    {
        $expander = new GreaterThan($boundary);
        $this->assertEquals($expectedResult, $expander->match($value));
    }

    public static function examplesProvider()
    {
        return array(
            array(10, 10.5, true),
            array(-20, -10.5, true),
            array(10, 1, false),
            array(1, 1, false),
            array(10, "20", true)
        );
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new GreaterThan($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return array(
            array(1, "ipsum lorem", "Value \"ipsum lorem\" is not a valid number."),
            array(10, 5, "Value \"5\" is not greater than \"10\"."),
            array(5, 5, "Value \"5\" is not greater than \"5\"."),
        );
    }
}
