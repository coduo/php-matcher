<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class EndsWith implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'endsWith';

    private string $stringEnding;

    private ?string $error = null;

    private bool $ignoreCase;

    public function __construct(string $stringEnding, bool $ignoreCase = false)
    {
        if (!\is_string($stringEnding)) {
            throw new \InvalidArgumentException('String ending must be a valid string.');
        }

        $this->stringEnding = $stringEnding;
        $this->ignoreCase = $ignoreCase;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        $this->backtrace->expanderEntrance(self::NAME, $value);

        if (!\is_string($value)) {
            $this->error = \sprintf('EndsWith expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        if (empty($this->stringEnding)) {
            $this->backtrace->expanderSucceed(self::NAME, $value);

            return true;
        }

        if (!$this->matchValue($value)) {
            $this->error = \sprintf("string \"%s\" doesn't ends with string \"%s\".", $value, $this->stringEnding);
            $this->backtrace->expanderFailed(self::NAME, $value, $this->error);

            return false;
        }

        $this->backtrace->expanderSucceed(self::NAME, $value);

        return true;
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    private function matchValue(string $value) : bool
    {
        return $this->ignoreCase
            ? \mb_substr(\mb_strtolower($value), -\mb_strlen(\mb_strtolower($this->stringEnding))) === \mb_strtolower($this->stringEnding)
            : \mb_substr($value, -\mb_strlen($this->stringEnding)) === $this->stringEnding;
    }
}
