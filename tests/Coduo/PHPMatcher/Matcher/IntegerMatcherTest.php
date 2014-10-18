<?php
namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Lexer;
use Coduo\PHPMatcher\Matcher\IntegerMatcher;
use Coduo\PHPMatcher\Parser;

class IntegerMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IntegerMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new IntegerMatcher(new Parser(new Lexer(), new Parser\ExpanderInitializer()));
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
            array("@integer@")
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array(10, "@integer@"),
            array(10, "@integer@.lowerThan(50).greaterThan(1)"),
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
