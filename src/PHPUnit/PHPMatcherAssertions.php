<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use PHPUnit\Framework\TestCase;

trait PHPMatcherAssertions
{
    protected function assertMatchesPattern(string $pattern, $value, string $message = '')
    {
        TestCase::assertThat($value, self::matchesPattern($pattern), $message);
    }

    protected static function matchesPattern(string $pattern) : PHPMatcherConstraint
    {
        return new PHPMatcherConstraint($pattern);
    }
}
