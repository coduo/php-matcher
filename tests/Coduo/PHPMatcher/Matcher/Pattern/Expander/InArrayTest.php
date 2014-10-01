<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\InArray;

class InArrayTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult)
    {
        $expander = new InArray($needle);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesProvider()
    {
        return array(
            array("ipsum", array("ipsum"), true),
            array(1, array("foo", 1), true),
            array(array("foo" => "bar"), array(array("foo" => "bar")), true),
        );
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new InArray($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return array(
            array("ipsum", array("ipsum lorem"), "Array(1) doesn't have \"ipsum\" element."),
            array("lorem", new \DateTime(), "InArray expander require \"array\", got \"\\DateTime\"."),
        );
    }
}
