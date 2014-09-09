<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\IntegerMatcher;

class IntegerMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new IntegerMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new IntegerMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new IntegerMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new IntegerMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new IntegerMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("@integer@")
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(10, "@integer@"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@integer"),
            array("integer"),
            array(1)
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array("1", "@integer@"),
            array(new \DateTime(),  "@integer@")
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array(new \stdClass,  "@integer@", "object \"\\stdClass\" is not a valid integer."),
            array(1.1, "@integer@", "double \"1.1\" is not a valid integer."),
            array(false, "@integer@", "boolean \"false\" is not a valid integer."),
            array(array('test'), "@integer@", "array \"Array(1)\" is not a valid integer.")
        );
    }
}
