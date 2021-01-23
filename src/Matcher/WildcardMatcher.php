<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;

final class WildcardMatcher extends Matcher
{
    /**
     * @var string
     */
    public const MATCH_PATTERN = "/^@(\*|wildcard)@$/";

    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        $result = \is_string($pattern) && 0 !== \preg_match(self::MATCH_PATTERN, $pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
