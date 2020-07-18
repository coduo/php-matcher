<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\PHPUnit;

use Coduo\PHPMatcher\Backtrace\InMemoryBacktrace;
use Coduo\PHPMatcher\PHPUnit\PHPMatcherAssertions;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;

class PHPMatcherAssertionsTest extends TestCase
{
    use PHPMatcherAssertions;

    public function test_it_asserts_if_a_value_matches_the_pattern() : void
    {
        $this->assertMatchesPattern('@string@', 'foo');
    }

    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern() : void
    {
        $this->expectException(AssertionFailedError::class);

        /*
         * Expected console output:
         *
         * Failed asserting that '{"foo":"bar"}' matches given pattern.
         * Pattern: '{"foo": "@integer@"}'
         * Error: Value {"foo":"bar"} does not match pattern {"foo":"@integer@"}
         * Backtrace:
         * #1 Matcher Coduo\PHPMatcher\Matcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
         * #2 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
         * #3 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "{"foo":"@integer@"}"
         * #...
         * #66 Matcher Coduo\PHPMatcher\Matcher error: Value {"foo":"bar"} does not match pattern {"foo":"@integer@"}.
         */
        $this->expectExceptionMessageMatches("/Failed asserting that '{\"foo\":\"bar\"}' matches given pattern.\nPattern: '{\"foo\": \"@integer@\"}'\nError: Value {\"foo\":\"bar\"} does not match pattern {\"foo\":\"@integer@\"}\nBacktrace: \nEmpty/");

        $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
    }

    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern_with_backtrace() : void
    {
        $this->expectException(AssertionFailedError::class);

        $this->setBacktrace($backtrace = new InMemoryBacktrace());

        /*
         * Expected console output:
         *
         * Failed asserting that '{"foo":"bar"}' matches given pattern.
         * Pattern: '{"foo": "@integer@"}'
         * Error: Value {"foo":"bar"} does not match pattern {"foo":"@integer@"}
         * Backtrace:
         * #1 Matcher Coduo\PHPMatcher\Matcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
         * #2 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
         * #3 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "{"foo":"@integer@"}"
         * #...
         * #66 Matcher Coduo\PHPMatcher\Matcher error: Value {"foo":"bar"} does not match pattern {"foo":"@integer@"}.
         */
        $this->expectExceptionMessageMatches("/Failed asserting that '{\"foo\":\"bar\"}' matches given pattern.\nPattern: '{\"foo\": \"@integer@\"}'\nError: Value {\"foo\":\"bar\"} does not match pattern {\"foo\":\"@integer@\"}\nBacktrace: \n/");

        $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
        $this->assertFalse($backtrace->isEmpty());
    }

    public function test_it_creates_a_constraint_for_stubs() : void
    {
        $this->expectException(AssertionFailedError::class);

        /*
         *  Expected console output:
         *
         *  Expectation failed for method name is "getTitle" when invoked zero or more time s
         *  Parameter 0 for invocation stdClass::getTitle(42) does not match expected value.
         *  Failed asserting that 42 matches given pattern.
         *  Pattern: '@string@'
         *  Error: integer "42" is not a valid string.
         *  Backtrace:
         *  #1 Matcher Coduo\PHPMatcher\Matcher matching value "42" with "@string@" pattern
         *  #2 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) matching value "42" with "@string@" pattern
         *  #3 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@string@"
         *  #...
         *  #35 Matcher Coduo\PHPMatcher\Matcher error: integer "42" is not a valid string.
         */
        $this->expectExceptionMessageMatches("/Expectation failed for method name is \"getTitle\" when invoked zero or more times\nParameter 0 for invocation stdClass::getTitle\(42\) does not match expected value.\nFailed asserting that 42 matches given pattern.\nPattern: '@string@'\nError: integer \"42\" is not a valid string.\nBacktrace: \n(.*)/");

        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['getTitle'])
            ->getMock();

        $mock->method('getTitle')
            ->with($this->matchesPattern('@string@'))
            ->willReturn('foo');

        $mock->getTitle(42);
    }
}
