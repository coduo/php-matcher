<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class StartsWith implements PatternExpander
{
    const NAME = 'startsWith';

    private $stringBeginning;

    private $error;

    private $ignoreCase;

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
        if (!\is_string($value)) {
            $this->error = \sprintf('StartsWith expander require "string", got "%s".', new StringConverter($value));
            return false;
        }

        if (empty($this->stringBeginning)) {
            return true;
        }

        if ($this->matchValue($value)) {
            $this->error = \sprintf("string \"%s\" doesn't starts with string \"%s\".", $value, $this->stringBeginning);
            return false;
        }

        return true;
    }

    public function getError()
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
