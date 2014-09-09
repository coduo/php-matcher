<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\DoubleMatcher;

class DoubleMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new DoubleMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new DoubleMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new DoubleMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new DoubleMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new DoubleMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("@double@")
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(10.1, "@double@"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@double"),
            array("double"),
            array(1)
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array("1", "@double@"),
            array(new \DateTime(),  "@double@"),
            array(10,  "@double@")
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array(new \stdClass,  "@integer@", "object \"\\stdClass\" is not a valid double."),
            array(25, "@integer@", "integer \"25\" is not a valid double."),
            array(false, "@integer@", "boolean \"false\" is not a valid double."),
            array(array('test'), "@integer@", "array \"Array(1)\" is not a valid double.")
        );
    }
}
