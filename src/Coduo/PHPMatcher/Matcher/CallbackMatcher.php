<?php

namespace Coduo\PHPMatcher\Matcher;

class CallbackMatcher extends Matcher
{
    /**
     * {@inheritdoc}
     */
    public function match($value, $pattern)
    {
        return (boolean) $pattern->__invoke($value);
    }

    /**
     * {@inheritdoc}
     */
    public function canMatch($pattern)
    {
        return is_callable($pattern);
    }
}
