<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\PHPUnit;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use PHPUnit\Framework\TestCase;

class PHPMatcherAssertionsTest extends TestCase
{
    use PHPMatcherAssertions;

    public function test_it_asserts_if_a_value_matches_the_pattern()
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }

    /**
     * @expectedException \PHPUnit\Framework\AssertionFailedError
     * @expectedExceptionMessage Failed asserting that '{"foo":"bar"}' matches the pattern
     */
    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern()
    {
        $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
    }

    /**
     * @expectedException \PHPUnit\Framework\AssertionFailedError
     * @expectedExceptionMessage Failed asserting that 42 matches the pattern.
     */
    public function test_it_creates_a_constraint_for_stubs()
    {
        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['getTitle'])
            ->getMock();

        $mock->method('getTitle')
            ->with($this->matchesPattern('@string@'))
            ->willReturn('foo');

        $mock->getTitle(42);
    }
}
