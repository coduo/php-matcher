<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\BooleanMatcher;

class BooleanMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new BooleanMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new BooleanMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new BooleanMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new BooleanMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new BooleanMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("@boolean@")
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(true, "@boolean@"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@boolean"),
            array("boolean"),
            array(1)
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array("1", "@boolean@"),
            array(new \DateTime(),  "@boolean@")
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array(new \stdClass,  "@boolean@", "object \"\\stdClass\" is not a valid boolean."),
            array(1.1, "@boolean@", "double \"1.1\" is not a valid boolean."),
            array("true", "@string@", "string \"true\" is not a valid boolean."),
            array(array('test'), "@boolean@", "array \"Array(1)\" is not a valid boolean.")
        );
    }
}
