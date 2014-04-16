<?php
namespace JsonMatcher\Tests\Matcher;

use JsonMatcher\Matcher\WildcardMatcher;

class WildcardMatcherTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider data
     */
    public function test_type_placeholders($pattern)
    {
        $matcher = new WildcardMatcher();
        $this->assertTrue($matcher->match('*', $pattern));
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
}
