<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

final class WildcardMatcher extends Matcher
{
    const MATCH_PATTERN = "/^@(\*|wildcard)@$/";

    public function match($matcher, $pattern) : bool
    {
        return true;
    }

    public function canMatch($pattern) : bool
    {
        return \is_string($pattern) && 0 !== \preg_match(self::MATCH_PATTERN, $pattern);
    }
}
