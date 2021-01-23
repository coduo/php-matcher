<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class BooleanMatcher extends Matcher
{
    public const PATTERN = 'boolean';

    private \Coduo\PHPMatcher\Backtrace $backtrace;

    private \Coduo\PHPMatcher\Parser $parser;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->parser = $parser;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!\is_bool($value)) {
            $this->error = \sprintf('%s "%s" is not a valid boolean.', \gettype($value), new StringConverter($value));
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
