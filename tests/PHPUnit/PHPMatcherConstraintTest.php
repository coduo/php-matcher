<?php

namespace Coduo\PHPMatcher\Tests\PHPUnit;

use Coduo\PHPMatcher\PHPUnit\PHPMatcherConstraint;

class PHPMatcherConstraintTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_is_a_phpunit_constraint()
    {
        $this->assertInstanceOf('PHPUnit_Framework_Constraint', new PHPMatcherConstraint('@string@'));
    }

    public function test_it_returns_true_if_a_value_matches_the_pattern()
    {
        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertTrue($constraint->evaluate('foo', '', true));
    }

    public function test_it_returns_false_if_a_value_does_not_match_the_pattern()
    {
        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertFalse($constraint->evaluate(42, '', true));
    }

    public function test_it_returns_false_if_a_pattern_is_not_a_string()
    {
        $constraint = new PHPMatcherConstraint(new \stdClass());

        $this->assertFalse($constraint->evaluate('foo', '', true));
    }

    /**
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage Failed asserting that 42 matches the pattern
     */
    public function test_it_sets_a_failure_description_if_not_given()
    {
        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertFalse($constraint->evaluate(42));
    }

    /**
     * @expectedException \PHPUnit_Framework_ExpectationFailedException
     * @expectedExceptionMessage integer "42" is not a valid string
     */
    public function test_it_sets_additional_failure_description()
    {
        $constraint = new PHPMatcherConstraint('@string@');

        $this->assertFalse($constraint->evaluate(42));
    }
}
