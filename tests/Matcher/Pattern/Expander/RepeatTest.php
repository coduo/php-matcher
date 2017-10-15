<?php

namespace Coduo\PHPMatcher\Tests\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\Expander\Repeat;

class RepeatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider examplesProvider
     */
    public function test_matching_values($needle, $haystack, $expectedResult, $isStrict = true)
    {
        $expander = new Repeat($needle, $isStrict);
        $this->assertEquals($expectedResult, $expander->match($haystack));
    }

    public static function examplesProvider()
    {
        $jsonPattern = '{"name": "@string@", "activated": "@boolean@"}';

        $jsonTest = array(
            array("name" => "toto", "activated" => true),
            array("name" => "titi", "activated" => false),
            array("name" => "tate", "activated" => true)
        );

        $scalarPattern = "@string@";
        $scalarTest = array(
            "toto",
            "titi",
            "tata"
        );

        $strictTest = array(
            array("name" => "toto", "activated" => true, "offset" => "offset")
        );

        return array(
            array($jsonPattern, $jsonTest, true),
            array($scalarPattern, $scalarTest, true),
            array($jsonPattern, $strictTest, true, false)
        );
    }

    /**
     * @dataProvider invalidCasesProvider
     */
    public function test_error_when_matching_fail($boundary, $value, $errorMessage)
    {
        $expander = new Repeat($boundary);
        $this->assertFalse($expander->match($value));
        $this->assertEquals($errorMessage, $expander->getError());
    }

    public static function invalidCasesProvider()
    {
        $pattern = '{"name": "@string@", "activated": "@boolean@"}';

        $valueTest = array(
            array("name" => 1, "activated" => "yes")
        );

        $keyTest = array(
            array("offset" => true, "foe" => "bar")
        );

        $strictTest = array(
            array("name" => 1, "activated" => "yes", "offset" => true)
        );

        return array(
            array($pattern, $valueTest, 'Repeat expander, entry n°0, key "name", find error : integer "1" is not a valid string.'),
            array($pattern, $keyTest, 'Repeat expander, entry n°0, require "array" to have key "name".'),
            array($pattern, $strictTest, 'Repeat expander expect to have 2 keys in array but get : 3'),
            array($pattern, "", 'Repeat expander require "array", got "".')
        );
    }
}
