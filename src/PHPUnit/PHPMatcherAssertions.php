<?php

namespace Coduo\PHPMatcher\PHPUnit;

trait PHPMatcherAssertions
{
    /**
     * @param string $pattern
     * @param mixed  $value
     * @param string $message
     */
    protected function assertMatchesPattern($pattern, $value, $message = '')
    {
        \PHPUnit_Framework_TestCase::assertThat($value, self::matchesPattern($pattern), $message);
    }

    /**
     * @param string $pattern
     *
     * @return PHPMatcherConstraint
     */
    protected static function matchesPattern($pattern)
    {
        return new PHPMatcherConstraint($pattern);
    }
}