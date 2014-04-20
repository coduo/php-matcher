<?php
namespace PHPMatcher\Tests\Matcher;

use PHPMatcher\Matcher\CaptureMatcher;

class CaptureMatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider positiveCanMatchData
     */
    public function test_positive_can_matches($pattern)
    {
        $matcher = new CaptureMatcher();
        $this->assertTrue($matcher->canMatch($pattern));
    }

    /**
     * @dataProvider negativeCanMatchData
     */
    public function test_negative_can_matches($pattern)
    {
        $matcher = new CaptureMatcher();
        $this->assertFalse($matcher->canMatch($pattern));
    }

    public function test_capturing()
    {
        $matcher = new CaptureMatcher();

        $matcher->match(50, ':userId:');
        $matcher->match('1111-qqqq-eeee-xxxx', ':token:');
        $this->assertEquals($matcher['userId'], 50);
        $this->assertEquals($matcher['token'], '1111-qqqq-eeee-xxxx');
    }

    public static function positiveCanMatchData()
    {
        return array(
            array(":id:"),
            array(":user_id:"),
            array(":foobar:")
        );
    }

    public static function negativeCanMatchData()
    {
        return array(
            array(":user_id"),
            array("foobar"),
            array(1),
            array("user_id:"),
            array(new \stdClass),
            array(array("foobar"))
        );
    }

}
