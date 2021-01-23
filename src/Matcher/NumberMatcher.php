<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class NumberMatcher extends Matcher
{
    /**
     * @var string
     */
    public const PATTERN = 'number';

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

        if (!\is_numeric($value)) {
            $this->error = \sprintf('%s "%s" is not a valid number.', \gettype($value), new StringConverter($value));
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

        $result = $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::PATTERN);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
