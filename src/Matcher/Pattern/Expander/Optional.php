<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

final class Optional implements PatternExpander
{
    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public function match($value)
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return null;
    }
}
