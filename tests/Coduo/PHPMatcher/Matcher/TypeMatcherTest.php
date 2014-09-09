<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\TypeMatcher;

class TypeMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new TypeMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new TypeMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $matcher = new TypeMatcher();
        $this->assertTrue($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $matcher = new TypeMatcher();
        $this->assertFalse($matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $matcher = new TypeMatcher();
        $matcher->match($value, $pattern);
        $this->assertEquals($error, $matcher->getError());
    }

    public static function positiveCanMatchData()
    {
        return array(
            array("@integer@"),
            array("@string@"),
            array("@boolean@"),
            array("@double@"),
            array("@array@")
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(false, "@boolean@"),
            array("Norbert", "@string@"),
            array(1, "@integer@"),
            array(6.66, "@double@"),
            array(array('test'), '@array@')
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@integer"),
            array("qweqwe"),
            array(1),
            array("@string"),
            array(new \stdClass),
            array(array("foobar"))
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array("test", "@boolean@"),
            array(new \stdClass,  "@string@"),
            array(1.1, "@integer@"),
            array(false, "@double@"),
            array(1, "@array@")
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array("test", "@boolean@", "string \"test\" does not match @boolean@ pattern."),
            array(new \stdClass,  "@string@", "object \"\\stdClass\" does not match @string@ pattern."),
            array(1.1, "@integer@", "double \"1.1\" does not match @integer@ pattern."),
            array(false, "@double@", "boolean \"false\" does not match @double@ pattern."),
            array(1, "@array@", "integer \"1\" does not match @array@ pattern.")
        );
    }
}
