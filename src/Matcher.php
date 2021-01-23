<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher;

use Coduo\PHPMatcher\Matcher\ValueMatcher;

final class Matcher
{
    private ValueMatcher $valueMatcher;

    private Backtrace $backtrace;

    public function __construct(ValueMatcher $valueMatcher, Backtrace $backtrace)
    {
        $this->valueMatcher = $valueMatcher;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        $result = $this->valueMatcher->match($value, $pattern);

        if ($result) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);
            $this->valueMatcher->clearError();
        } else {
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->valueMatcher->getError());
        }

        return $result;
    }

    /**
     * @return null|string
     */
    public function getError() : ?string
    {
        return $this->valueMatcher->getError();
    }

    public function backtrace() : Backtrace
    {
        return $this->backtrace;
    }
}
