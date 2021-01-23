<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;

final class NullMatcher extends Matcher
{
    /**
     * @var string
     */
    public const MATCH_PATTERN = '/^@null@$/';

    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (null !== $value) {
            $this->error = \sprintf('%s "%s" does not match null.', \gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern) : bool
    {
        $result = null === $pattern || (\is_string($pattern) && 0 !== \preg_match(self::MATCH_PATTERN, $pattern));
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
