<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\String;

class ScalarMatcher extends Matcher
{
    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if ($value !== $pattern) {
            $this->error = sprintf("\"%s\" does not match \"%s\".", new String($value), new String($pattern));
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_scalar($pattern);
    }
}
