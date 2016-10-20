<?php

namespace Coduo\PHPMatcher\Tests\Matcher;

use Coduo\PHPMatcher\Matcher\UuidMatcher;

class UuidMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UuidMatcher
     */
    private $matcher;

    public function setUp()
    {
        $this->matcher = new UuidMatcher();
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
            array("@uuid@"),
        );
    }

    public static function positiveMatchData()
    {
        return array(
            array("9f4db639-0e87-4367-9beb-d64e3f42ae18", "@uuid@"),
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array("@uuid"),
            array("uuid"),
            array(1),
        );
    }

    public static function negativeMatchData()
    {
        return array(
            array(1, "@uuid@"),
            array(0, "@uuid@"),
            array("9f4d-b639-0e87-4367-9beb-d64e3f42ae18", "@uuid@"),
            array("9f4db639-0e87-4367-9beb-d64e3f42ae1", "@uuid@"),
            array("9f4db639-0e87-4367-9beb-d64e3f42ae181", "@uuid@"),
            array("9f4db6390e8743679bebd64e3f42ae18", "@uuid@"),
            array("9f4db6390e87-4367-9beb-d64e-3f42ae18", "@uuid@"),
            array("9f4db639-0e87-4367-9beb-d64e3f42ae1g", "@uuid@"),
            array("9f4db639-0e87-0367-9beb-d64e3f42ae18", "@uuid@"),
        );
    }

    public static function negativeMatchDescription()
    {
        return array(
            array(new \stdClass,  "@uuid@", "object \"\\stdClass\" is not a valid UUID: not a string."),
            array(1.1, "@uuid@", "double \"1.1\" is not a valid UUID: not a string."),
            array(false, "@uuid@", "boolean \"false\" is not a valid UUID: not a string."),
            array(1, "@uuid@", "integer \"1\" is not a valid UUID: not a string."),
            array("lorem ipsum", "@uuid@", "string \"lorem ipsum\" is not a valid UUID: invalid format."),
            array("9f4db639-0e87-4367-9beb-d64e3f42ae1z", "@uuid@", "string \"9f4db639-0e87-4367-9beb-d64e3f42ae1z\" is not a valid UUID: invalid format."),
        );
    }
}
