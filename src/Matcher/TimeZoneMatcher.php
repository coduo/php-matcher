<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Aeon\Calendar\Gregorian\TimeZone;
use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class TimeZoneMatcher extends Matcher
{
    /**
     * @var string
     */
    public const PATTERN = 'timezone';

    /**
     * @var string
     */
    public const PATTERN_SHORT = 'tz';

    private Backtrace $backtrace;

    private Parser $parser;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->backtrace = $backtrace;
        $this->parser = $parser;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!\is_string($value)) {
            $this->error = \sprintf('%s "%s" is not a valid string.', \gettype($value), new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        try {
            /** @phpstan-ignore-next-line  */
            TimeZone::fromString($value);
        } catch (\Exception $exception) {
            $this->error = \sprintf('%s "%s" is not a valid timezone.', $value, new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $typePattern = $this->parser->parse($pattern);

        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        if (!\is_string($pattern)) {
            $this->backtrace->matcherCanMatch(self::class, $pattern, false);

            return false;
        }

        $result = $this->parser->hasValidSyntax($pattern) && ($this->parser->parse($pattern)->is(self::PATTERN) || $this->parser->parse($pattern)->is(self::PATTERN_SHORT));
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
