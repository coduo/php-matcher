<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\WildcardMatcher;

class WildcardMatcherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider data
     */
    public function test_positive_match($pattern)
    {
        $matcher = new WildcardMatcher();
        $this->assertTrue($matcher->match('*', $pattern));
    }

    /**
     * @dataProvider positivePatterns
     */
    public function test_positive_can_match($pattern)
    {
        $matcher = new WildcardMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    public function test_negative_can_match()
    {
        $matcher = new WildcardMatcher();
        $this->assertFalse($matcher->canMatch('*'));
    }

    public static function data()
    {
        return array(
            array("@integer@"),
            array("foobar"),
            array(true),
            array(6.66),
            array(array("bar")),
            array(new \stdClass),
        );
    }

    public static function positivePatterns()
    {
        return array(
            array("@*@"),
            array("@wildcard@"),
        );
    }
}
