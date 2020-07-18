<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\PHPUnit;

use PHPUnit\Framework\TestCase;

abstract class PHPMatcherTestCase extends TestCase
{
    protected function assertMatchesPattern($pattern, $value, string $message = '') : void
    {
        $this->assertThat($value, self::matchesPattern($pattern), $message);
    }

    protected static function matchesPattern($pattern) : PHPMatcherConstraint
    {
        return new PHPMatcherConstraint($pattern);
    }
}
