<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\StringMatcher;

class StringMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new StringMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new StringMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new StringMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new StringMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new StringMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("@string@")
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array("lorem ipsum", "@string@"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@string"),
            array("string"),
            array(1)
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array(1, "@string@"),
            array(0,  "@string@")
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array(new \stdClass,  "@string@", "object \"\\stdClass\" is not a valid string."),
            array(1.1, "@integer@", "double \"1.1\" is not a valid string."),
            array(false, "@double@", "boolean \"false\" is not a valid string."),
            array(1, "@array@", "integer \"1\" is not a valid string.")
        );
    }
}
