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
        try {
            $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
        } catch (\Exception $e) {
            $this->assertSame(
                <<<'ERROR'
Failed asserting that Value "bar" does not match pattern "@integer@" at path: "[foo]".
ERROR,
                $e->getMessage()
            );
        }
    }

    public function test_it_throws_an_expectation_failed_exception_if_a_value_does_not_match_the_pattern_with_backtrace() : void
    {
        $this->setBacktrace($backtrace = new InMemoryBacktrace());

        try {
            $this->assertMatchesPattern('{"foo": "@integer@"}', \json_encode(['foo' => 'bar']));
        } catch (\Exception $e) {
            $this->assertSame(
                <<<ERROR
Failed asserting that Value "bar" does not match pattern "@integer@" at path: "[foo]"
Backtrace:
#1 Matcher Coduo\PHPMatcher\Matcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#2 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#3 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "{"foo":"@integer@"}"
#4 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#5 Matcher Coduo\PHPMatcher\Matcher\CallbackMatcher can't match pattern "{"foo":"@integer@"}"
#6 Matcher Coduo\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "{"foo":"@integer@"}"
#7 Matcher Coduo\PHPMatcher\Matcher\NullMatcher can't match pattern "{"foo":"@integer@"}"
#8 Matcher Coduo\PHPMatcher\Matcher\StringMatcher can't match pattern "{"foo":"@integer@"}"
#9 Matcher Coduo\PHPMatcher\Matcher\IntegerMatcher can't match pattern "{"foo":"@integer@"}"
#10 Matcher Coduo\PHPMatcher\Matcher\BooleanMatcher can't match pattern "{"foo":"@integer@"}"
#11 Matcher Coduo\PHPMatcher\Matcher\DoubleMatcher can't match pattern "{"foo":"@integer@"}"
#12 Matcher Coduo\PHPMatcher\Matcher\NumberMatcher can't match pattern "{"foo":"@integer@"}"
#13 Matcher Coduo\PHPMatcher\Matcher\TimeMatcher can't match pattern "{"foo":"@integer@"}"
#14 Matcher Coduo\PHPMatcher\Matcher\DateMatcher can't match pattern "{"foo":"@integer@"}"
#15 Matcher Coduo\PHPMatcher\Matcher\DateTimeMatcher can't match pattern "{"foo":"@integer@"}"
#16 Matcher Coduo\PHPMatcher\Matcher\TimeZoneMatcher can't match pattern "{"foo":"@integer@"}"
#17 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher can match pattern "{"foo":"@integer@"}"
#18 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#19 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#20 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher error: "{"foo":"bar"}" does not match "{"foo":"@integer@"}".
#21 Matcher Coduo\PHPMatcher\Matcher\WildcardMatcher can't match pattern "{"foo":"@integer@"}"
#22 Matcher Coduo\PHPMatcher\Matcher\UuidMatcher can't match pattern "{"foo":"@integer@"}"
#23 Matcher Coduo\PHPMatcher\Matcher\JsonObjectMatcher can't match pattern "{"foo":"@integer@"}"
#24 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#25 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) error: "{"foo":"bar"}" does not match "{"foo":"@integer@"}".
#26 Matcher Coduo\PHPMatcher\Matcher\JsonMatcher can match pattern "{"foo":"@integer@"}"
#27 Matcher Coduo\PHPMatcher\Matcher\JsonMatcher matching value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#28 Matcher Coduo\PHPMatcher\Matcher\ArrayMatcher matching value "Array(1)" with "Array(1)" pattern
#29 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (array) can match pattern "@integer@"
#30 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (array) matching value "bar" with "@integer@" pattern
#31 Matcher Coduo\PHPMatcher\Matcher\OrMatcher can't match pattern "@integer@"
#32 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) can match pattern "@integer@"
#33 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) matching value "bar" with "@integer@" pattern
#34 Matcher Coduo\PHPMatcher\Matcher\CallbackMatcher can't match pattern "@integer@"
#35 Matcher Coduo\PHPMatcher\Matcher\ExpressionMatcher can't match pattern "@integer@"
#36 Matcher Coduo\PHPMatcher\Matcher\NullMatcher can't match pattern "@integer@"
#37 Matcher Coduo\PHPMatcher\Matcher\StringMatcher can't match pattern "@integer@"
#38 Matcher Coduo\PHPMatcher\Matcher\IntegerMatcher can match pattern "@integer@"
#39 Matcher Coduo\PHPMatcher\Matcher\IntegerMatcher matching value "bar" with "@integer@" pattern
#40 Matcher Coduo\PHPMatcher\Matcher\IntegerMatcher failed to match value "bar" with "@integer@" pattern
#41 Matcher Coduo\PHPMatcher\Matcher\IntegerMatcher error: string "bar" is not a valid integer.
#42 Matcher Coduo\PHPMatcher\Matcher\BooleanMatcher can't match pattern "@integer@"
#43 Matcher Coduo\PHPMatcher\Matcher\DoubleMatcher can't match pattern "@integer@"
#44 Matcher Coduo\PHPMatcher\Matcher\NumberMatcher can't match pattern "@integer@"
#45 Matcher Coduo\PHPMatcher\Matcher\TimeMatcher can't match pattern "@integer@"
#46 Matcher Coduo\PHPMatcher\Matcher\DateMatcher can't match pattern "@integer@"
#47 Matcher Coduo\PHPMatcher\Matcher\DateTimeMatcher can't match pattern "@integer@"
#48 Matcher Coduo\PHPMatcher\Matcher\TimeZoneMatcher can't match pattern "@integer@"
#49 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher can match pattern "@integer@"
#50 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher matching value "bar" with "@integer@" pattern
#51 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher failed to match value "bar" with "@integer@" pattern
#52 Matcher Coduo\PHPMatcher\Matcher\ScalarMatcher error: "bar" does not match "@integer@".
#53 Matcher Coduo\PHPMatcher\Matcher\WildcardMatcher can't match pattern "@integer@"
#54 Matcher Coduo\PHPMatcher\Matcher\UuidMatcher can't match pattern "@integer@"
#55 Matcher Coduo\PHPMatcher\Matcher\JsonObjectMatcher can't match pattern "@integer@"
#56 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) failed to match value "bar" with "@integer@" pattern
#57 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (scalars) error: "bar" does not match "@integer@".
#58 Matcher Coduo\PHPMatcher\Matcher\TextMatcher can match pattern "@integer@"
#59 Matcher Coduo\PHPMatcher\Matcher\TextMatcher matching value "bar" with "@integer@" pattern
#60 Matcher Coduo\PHPMatcher\Matcher\TextMatcher failed to match value "bar" with "@integer@" pattern
#61 Matcher Coduo\PHPMatcher\Matcher\TextMatcher error: "bar" does not match "@integer@" pattern
#62 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (array) failed to match value "bar" with "@integer@" pattern
#63 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (array) error: "bar" does not match "@integer@" pattern
#64 Matcher Coduo\PHPMatcher\Matcher\ArrayMatcher failed to match value "Array(1)" with "Array(1)" pattern
#65 Matcher Coduo\PHPMatcher\Matcher\ArrayMatcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#66 Matcher Coduo\PHPMatcher\Matcher\JsonMatcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#67 Matcher Coduo\PHPMatcher\Matcher\JsonMatcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#68 Matcher Coduo\PHPMatcher\Matcher\XmlMatcher can't match pattern "{"foo":"@integer@"}"
#69 Matcher Coduo\PHPMatcher\Matcher\OrMatcher can't match pattern "{"foo":"@integer@"}"
#70 Matcher Coduo\PHPMatcher\Matcher\TextMatcher can't match pattern "{"foo":"@integer@"}"
#71 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#72 Matcher Coduo\PHPMatcher\Matcher\ChainMatcher (all) error: Value "bar" does not match pattern "@integer@" at path: "[foo]"
#73 Matcher Coduo\PHPMatcher\Matcher failed to match value "{"foo":"bar"}" with "{"foo":"@integer@"}" pattern
#74 Matcher Coduo\PHPMatcher\Matcher error: Value "bar" does not match pattern "@integer@" at path: "[foo]".
ERROR,
                $e->getMessage()
            );
        }

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
        $this->expectExceptionMessageMatches("/Expectation failed for method name is \"getTitle\" when invoked zero or more times\nParameter 0 for invocation stdClass::getTitle\(42\) does not match expected value.\nFailed asserting that integer \"42\" is not a valid string../");

        $mock = $this->getMockBuilder('stdClass')
            ->setMethods(['getTitle'])
            ->getMock();

        $mock->method('getTitle')
            ->with($this->matchesPattern('@string@'))
            ->willReturn('foo');

        $mock->getTitle(42);
    }
}
