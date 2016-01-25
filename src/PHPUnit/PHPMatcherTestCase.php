<?php

namespace Coduo\PHPMatcher\PHPUnit;

abstract class PHPMatcherTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $pattern
     * @param mixed  $value
     * @param string $message
     */
    protected function assertMatchesPattern($pattern, $value, $message = '')
    {
        $this->assertThat($value, self::matchesPattern($pattern), $message);
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