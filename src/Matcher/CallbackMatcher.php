<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;

final class CallbackMatcher extends Matcher
{
    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);
        $result = (bool) $pattern->__invoke($value);

        if ($result) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);
        } else {
            $this->error = \sprintf('Callback matcher failed for value %s', new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);
        }

        return $result;
    }

    public function canMatch($pattern) : bool
    {
        $result = \is_object($pattern) && \is_callable($pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
