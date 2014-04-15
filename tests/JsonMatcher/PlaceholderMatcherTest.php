<?php
namespace JsonMatcher\Tests;

use JsonMatcher\Matcher\TypeMatcher;

class TypeMatcherTest extends \PHPUnit_Framework_TestCase
{

    public function test_type_placeholders()
    {
        $matcher = new TypeMatcher();
        $this->assertTrue($matcher->match(1, "@integer"));
        $this->assertTrue($matcher->match("michal", "@string"));
        $this->assertTrue($matcher->match(false, "@boolean"));
        $this->assertTrue($matcher->match(6.66, "@double"));
    }
}

