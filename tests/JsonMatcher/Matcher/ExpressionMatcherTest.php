<?php
namespace JsonMatcher\Tests\Matcher;

use JsonMatcher\Matcher\ExpressionMatcher;

class ExpressionMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new ExpressionMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("expr(1 > 2)"),
            array("expr(value == 'foo')"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@integer"),
            array("expr("),
            array("@string"),
            array(new \stdClass),
            array(array("foobar"))
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(4, "expr(value > 2)"),
            array("foo", "expr(value == 'foo')"),
            array(new \DateTime('2014-04-01'), "expr(value.format('Y-m-d') == '2014-04-01')")
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array(4, "expr(value < 2)"),
            array("foo", "expr(value != 'foo')"),
        );
    }
}
