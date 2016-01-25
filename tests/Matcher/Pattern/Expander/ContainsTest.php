<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher;
use Coduo\PHPMatcher\Matcher\Pattern\Expander\Contains;

class ContainsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesIgnoreCaseProvider
     */
    public function test_matching_values_ignore_case($needle, $haystack, $expectedResult)
    {
        $expander = new Contains($needle);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesIgnoreCaseProvider()
    {
        return array(
            array("ipsum", "lorem ipsum", true),
            array("wor", "this is my hello world string", true),
            array("lol", "lorem ipsum", false),
            array("NO", "norbert", false)
        );
    }

    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult)
    {
        $expander = new Contains($needle, true);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesProvider()
    {
        return array(
            array("IpSum", "lorem ipsum", true),
            array("wor", "this is my hello WORLD string", true),
            array("lol", "LOREM ipsum", false),
            array("NO", "NORBERT", true)
        );
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($string, $value, $errorMessage)
    {
        $expander = new Contains($string);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        return array(
            array("ipsum", "hello world", "String \"hello world\" doesn't contains \"ipsum\"."),
            array("lorem", new \DateTime(), "Contains expander require \"string\", got \"\\DateTime\"."),
        );
    }
}
