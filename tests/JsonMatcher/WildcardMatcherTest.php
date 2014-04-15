<?php
namespace JsonMatcher\Tests;

use JsonMatcher\Matcher\WildcardMatcher;

class WildcardMatcherTest extends \PHPUnit_Framework_TestCase
{

    public function test_type_placeholders()
    {
        $matcher = new WildcardMatcher();
        $this->assertTrue($matcher->match('*', "@integer"));
        $this->assertTrue($matcher->match("*", "foobar"));
        $this->assertTrue($matcher->match("*", true));
        $this->assertTrue($matcher->match("*", 6.66));
    }
}
