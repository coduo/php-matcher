<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use PHPUnit\Framework\TestCase;

trait PHPMatcherAssertions
{
    protected function assertMatchesPattern($pattern, $value, string $message = '') : void
    {
        TestCase::assertThat($value, self::matchesPattern($pattern), $message);
    }

    protected static function matchesPattern($pattern) : PHPMatcherConstraint
    {
        return new PHPMatcherConstraint($pattern);
    }
}
