<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

final class CallbackMatcher extends Matcher
{
    public function match($value, $pattern) : bool
    {
        return (boolean) $pattern->__invoke($value);
    }

    public function canMatch($pattern) : bool
    {
        return \is_object($pattern) && \is_callable($pattern);
    }
}
