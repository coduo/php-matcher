<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\NumberMatcher;

class NumberMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new NumberMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new NumberMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("@number@")
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(10.1, "@number@"),
            array(10, "@number@"),
            array("25", "@number@"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@number"),
            array("number"),
            array(1)
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array(array("test"), "@number@"),
            array(new \DateTime(),  "@number@"),
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array(new \stdClass,  "@number@", "object \"\\stdClass\" is not a valid number."),
            array(false, "@number@", "boolean \"false\" is not a valid number."),
            array(array('test'), "@number@", "array \"Array(1)\" is not a valid number.")
        );
    }
}
