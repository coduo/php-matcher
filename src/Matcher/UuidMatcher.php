<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class UuidMatcher extends Matcher
{
    /**
     * @var string
     */
    public const PATTERN = 'uuid';

    /**
     * @var string
     */
    public const UUID_PATTERN = '[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}';

    /**
     * @var string
     */
    public const UUID_FORMAT_PATTERN = '|^' . self::UUID_PATTERN . '$|';

    private Backtrace $backtrace;

    private Parser $parser;

    public function __construct(Backtrace $backtrace, Parser $parser)
    {
        $this->parser = $parser;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (!\is_string($value)) {
            $this->error = \sprintf(
                '%s "%s" is not a valid UUID: not a string.',
                \gettype($value),
                new StringConverter($value)
            );
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (1 !== \preg_match(self::UUID_FORMAT_PATTERN, $value)) {
            $this->error = \sprintf(
                '%s "%s" is not a valid UUID: invalid format.',
                \gettype($value),
                $value
            );
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
