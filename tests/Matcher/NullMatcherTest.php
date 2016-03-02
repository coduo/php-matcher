<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\NullMatcher;

class NullMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new NullMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new NullMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new NullMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new NullMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new NullMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("@null@"),
            array(null)
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(null, "@null@"),
            array(null, null),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@null"),
            array("null"),
            array(0)
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array("null", "@null@"),
            array(0,  "@null@")
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array("test", "@boolean@", "string \"test\" does not match null."),
            array(new \stdClass,  "@string@", "object \"\\stdClass\" does not match null."),
            array(1.1, "@integer@", "double \"1.1\" does not match null."),
            array(false, "@double@", "boolean \"false\" does not match null."),
            array(1, "@array@", "integer \"1\" does not match null.")
        );
    }
}
