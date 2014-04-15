<?php 
namespace JsonMatcher\Tests;

use JsonMatcher\Matcher;

class ScalarMatcherTest extends \PHPUnit_Framework_TestCase
{
    public function test_match_scalars()
    {
        $matcher = new Matcher\ScalarMatcher();

        $this->assertTrue($matcher->match(1, 1));
        $this->assertTrue($matcher->match("michal", "michal"));
        $this->assertFalse($matcher->match(false, "false"));
        $this->assertFalse($matcher->match(false, 0));
    }
}
