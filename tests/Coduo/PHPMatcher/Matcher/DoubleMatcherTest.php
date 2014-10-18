<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\DoubleMatcher;
use Coduo\PHPMatcher\Parser;

class DoubleMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DoubleMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new DoubleMatcher(new Parser(new Lexer(), new Parser\ExpanderInitializer()));
    }

    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $this->assertTrue($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $this->assertFalse($this->matcher->canMatch($pattern));
    }

    /**
     * @dataProvider positiveMatchData
     */
    public function test_positive_match($value, $pattern)
    {
        $this->assertTrue($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchData
     */
    public function test_negative_match($value, $pattern)
    {
        $this->assertFalse($this->matcher->match($value, $pattern));
    }

    /**
     * @dataProvider negativeMatchDescription
     */
    public function test_negative_match_description($value, $pattern, $error)
    {
        $this->matcher->match($value, $pattern);
        $this->assertEquals($error, $this->matcher->getError());
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
            array(10.1, "@double@.lowerThan(50.12).greaterThan(10)"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@double"),
            array("double"),
            array(1),
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array("1", "@double@"),
            array(new \DateTime(),  "@double@"),
            array(10,  "@double@"),
            array(4.9, "@double@.greaterThan(5)"),
            array(4.9, "@double@.lowerThan(20).greaterThan(5)"),
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
