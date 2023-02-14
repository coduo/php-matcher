<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\PHPUnit;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherTestCase;
use PHPUnit\Framework\AssertionFailedError;

class PHPMatcherTestCaseTest extends PHPMatcherTestCase
{
    public function test_it_asserts_if_a_value_matches_the_pattern() : void
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }

    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern() : void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches('/Failed asserting that Value "bar" does not match pattern "@integer@" at path: "\\[foo\\]"./');

        $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
    }

    public function test_it_creates_a_constraint_for_stubs() : void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches('/Failed asserting that integer "42" is not a valid string../');

        $mock = $this->getMockBuilder('stdClass')
            ->addMethods(['getTitle'])
            ->getMock();

        $mock->method('getTitle')
            ->with($this->matchesPattern('@string@'))
            ->willReturn('foo');

        $mock->getTitle(42);
    }

    public function test_it_asserts_if_a_value_matches_the_array_pattern() : void
    {
        $this->assertMatchesPattern(['foo' => '@string@'], ['foo' => 'bar']);
    }
}
