<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class StartsWith implements PatternExpander
{
    use BacktraceBehavior;

    /**
     * @var string
     */
    public const NAME = 'startsWith';

    private string $stringBeginning;

    private ?string $error = null;

    private bool $ignoreCase;

    public function __construct(string $stringBeginning, bool $ignoreCase = false)
    {
        if (!\is_string($stringBeginning)) {
            throw new \InvalidArgumentException('String beginning must be a valid string.');
        }

        $this->stringBeginning = $stringBeginning;
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
            $this->error = \sprintf('StartsWith expander require "string", got "%s".', new StringConverter($value));
            $this->backtrace->expanderSucceed(self::NAME, $value);

            return false;
        }

        if (empty($this->stringBeginning)) {
            $this->backtrace->expanderSucceed(self::NAME, $value);

            return true;
        }

        if ($this->matchValue($value)) {
            $this->error = \sprintf("string \"%s\" doesn't starts with string \"%s\".", $value, $this->stringBeginning);
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
            ? \mb_stripos($value, $this->stringBeginning) !== 0
            : \mb_strpos($value, $this->stringBeginning) !== 0;
    }
}
