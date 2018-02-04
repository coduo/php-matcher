<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Contains implements PatternExpander
{
    const NAME = 'contains';

    private $error;

    private $string;

    private $ignoreCase;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function __construct(string $string, $ignoreCase = false)
    {
        $this->string = $string;
        $this->ignoreCase = $ignoreCase;
    }

    public function match($value) : bool
    {
        if (!\is_string($value)) {
            $this->error = \sprintf('Contains expander require "string", got "%s".', new StringConverter($value));
            return false;
        }

        $contains = $this->ignoreCase
            ? \mb_stripos($value, $this->string)
            : \mb_strpos($value, $this->string);

        if ($contains === false) {
            $this->error = \sprintf("String \"%s\" doesn't contains \"%s\".", $value, $this->string);
            return false;
        }

        return true;
    }

    public function getError()
    {
        return $this->error;
    }
}
