<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;

final class NumberMatcher extends Matcher
{
    const NUMBER_PATTERN = '/^@number@$/';

    public function match($value, $pattern) : bool
    {
        if (!is_numeric($value)) {
            $this->error = sprintf("%s \"%s\" is not a valid number.", gettype($value), new StringConverter($value));
            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        return is_string($pattern) && 0 !== preg_match(self::NUMBER_PATTERN, $pattern);
    }
}
