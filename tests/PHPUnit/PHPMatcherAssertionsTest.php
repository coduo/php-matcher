<?php

namespace Coduo\PHPMatcher\Tests\PHPUnit;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;

class PHPMatcherAssertionsTest extends \PHPUnit_Framework_TestCase
{
    use PHPMatcherAssertions;

    public function test_it_asserts_if_a_value_matches_the_pattern()
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }

    /**
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Failed asserting that '{"foo":"bar"}' matches the pattern
     */
    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern()
    {
        $this->assertMatchesPattern('{"foo": "@integer@"}', json_encode(array('foo' => 'bar')));
    }

    /**
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Failed asserting that 42 matches the pattern.
     */
    public function test_it_creates_a_constraint_for_stubs()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(array('getTitle'))
            ->getMock();

        $mock->method('getTitle')
            ->with($this->matchesPattern('@string@'))
            ->willReturn('foo');

        $mock->getTitle(42);
    }
}
