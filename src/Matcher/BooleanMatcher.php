<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;

final class BooleanMatcher extends Matcher
{
    const BOOLEAN_PATTERN = '/^@boolean@$/';

    public function match($value, $pattern) : bool
    {
        if (!is_bool($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid boolean.", gettype($value), new StringConverter($value));
            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        return is_string($pattern) && 0 !== preg_match(self::BOOLEAN_PATTERN, $pattern);
    }
}
