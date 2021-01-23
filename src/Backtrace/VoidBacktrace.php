<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Backtrace;

use Coduo\PHPMatcher\Backtrace;

final class VoidBacktrace implements Backtrace
{
    public function __toString() : string
    {
        return 'Empty';
    }

    public function matcherCanMatch(string $name, $value, bool $result) : void
    {
    }

    public function matcherEntrance(string $name, $value, $pattern) : void
    {
    }

    public function matcherSucceed(string $name, $value, $pattern) : void
    {
    }

    public function matcherFailed(string $name, $value, $pattern, string $error) : void
    {
    }

    public function expanderEntrance(string $name, $value) : void
    {
    }

    public function expanderSucceed(string $name, $value) : void
    {
    }

    public function expanderFailed(string $name, $value, string $error) : void
    {
    }

    public function isEmpty() : bool
    {
        return true;
    }

    /**
     * @return mixed[]
     */
    public function raw() : array
    {
        return [];
    }

    public function last() : ?string
    {
        return null;
    }
}
