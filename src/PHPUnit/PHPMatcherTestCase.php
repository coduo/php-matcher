<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use PHPUnit\Framework\TestCase;

abstract class PHPMatcherTestCase extends TestCase
{
    protected function assertMatchesPattern(string $pattern, $value, string $message = '')
    {
        $this->assertThat($value, self::matchesPattern($pattern), $message);
    }

    protected static function matchesPattern(string $pattern) : PHPMatcherConstraint
    {
        return new PHPMatcherConstraint($pattern);
    }
}
