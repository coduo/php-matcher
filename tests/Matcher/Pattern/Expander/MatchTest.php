<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\Match;

class MatchTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult)
    {
        $expander = new Match($needle);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesProvider()
    {
        return array(
            array("ipsum", array("ipsum"), true),
            array(1, array(1, 1, 1), true),
            array(array("foo" => "bar"), array(array("foo" => "bar")), true),
        );
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new Match($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return array(
            array("ipsum", array("ipsum lorem"), "\"ipsum lorem\" does not match \"ipsum\" pattern"),
            array("lorem", new \DateTime(), "Match expander require \"array\", got \"\DateTime\"."),
        );
    }
}
