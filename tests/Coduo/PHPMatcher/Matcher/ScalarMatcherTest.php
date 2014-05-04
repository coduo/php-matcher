<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\ScalarMatcher;

class ScalarMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatches
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatches
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatches
     */
    public function test_positive_matches($value, $pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatches
     */
    public function test_negative_matches($value, $pattern)
    {
        $matcher = new ScalarMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new ScalarMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function negativeMatches()
    {
        return array(
            array(false, "false"),
            array(false, 0),
            array(true, 1),
            array("array", array()),
        );
    }

    public static function positiveMatches()
    {
        return array(
            array(1, 1),
            array("michal", "michal"),
            array(false, false),
            array(6.66, 6.66),
        );
    }

    public static function positiveCanMatches()
    {
        return array(
            array(1),
            array("michal"),
            array(true),
            array(false),
            array(6.66),
        );
    }

    public static function negativeCanMatches()
    {
        return array(
            array(new \stdClass),
            array(array())
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array("test", "norbert", "\"test\" does not match \"norbert\"."),
            array(new \stdClass,  1, "\"\\stdClass\" does not match \"1\"."),
            array(1.1, false, "\"1.1\" does not match \"false\"."),
            array(false, array('foo', 'bar'), "\"false\" does not match \"Array(2)\"."),
        );
    }
}
