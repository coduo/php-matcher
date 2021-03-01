<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Tests\PHPUnit;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherConstraint;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class PHPMatcherConstraintTest extends TestCase
{
    public function test_it_is_a_phpunit_constraint() : void
    {
        $this->assertInstanceOf(Constraint::class, new PHPMatcherConstraint('@string@'));
    }

    public function test_it_returns_true_if_a_value_matches_the_pattern() : void
    {
        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertTrue($constraint->evaluate('foo', '', true));
    }

    public function test_it_returns_false_if_a_value_does_not_match_the_pattern() : void
    {
        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertFalse($constraint->evaluate(42, '', true));
    }

    public function test_it_sets_a_failure_description_if_not_given() : void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessageMatches('/Failed asserting that integer "42" is not a valid string../');

        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertFalse($constraint->evaluate(42));
    }

    public function test_it_sets_additional_failure_description() : void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('integer "42" is not a valid string');

        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertFalse($constraint->evaluate(42));
    }

    public function test_expected_as_string_is_sorted() : void
    {
        $constraint = new PHPMatcherConstraint('{"b": 2, "a": 1}');

        try {
            $constraint->evaluate('null');

            $this->fail();
        } catch (ExpectationFailedException $exception) {
            $this->assertSame(
                <<<'JSON'
{
    "a": 1,
    "b": 2
}
JSON
                ,
                $exception->getComparisonFailure()->getExpectedAsString()
            );
        }
    }

    public function test_actual_as_string_is_sorted() : void
    {
        $constraint = new PHPMatcherConstraint('{}');

        try {
            $constraint->evaluate('{"b": 2, "a": 1}');

            $this->fail();
        } catch (ExpectationFailedException $exception) {
            $this->assertSame(
                <<<'JSON'
{
    "a": 1,
    "b": 2
}
JSON
                ,
                $exception->getComparisonFailure()->getActualAsString()
            );
        }
    }

    public function test_that_pattern_could_be_an_array() : void
    {
        $constraint = new PHPMatcherConstraint(['foo' => '@string@']);

        $this->assertTrue($constraint->evaluate(['foo' => 'foo value'], '', true));
    }

    /**
     * @dataProvider invalidPatterns
     */
    public function test_that_pattern_could_be_only_a_string_or_an_array($pattern) : void
    {
        $this->expectException(\LogicException::class);

        new PHPMatcherConstraint($pattern);
    }

    public function invalidPatterns()
    {
        return [
            [true],
            [1],
            [1.1],
            [new \StdClass],
            [null],
            [\fopen('php://memory', 'r')],
        ];
    }
}
