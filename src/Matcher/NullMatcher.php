<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;

final class NullMatcher extends Matcher
{
    const MATCH_PATTERN = '/^@null@$/';

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern) : bool
    {
        if (null !== $value) {
            $this->error = \sprintf('%s "%s" does not match null.', \gettype($value), new StringConverter($value));
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern) : bool
    {
        return \is_null($pattern) || (\is_string($pattern) && 0 !== \preg_match(self::MATCH_PATTERN, $pattern));
    }
}
