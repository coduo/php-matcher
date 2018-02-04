<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class IsUrl implements PatternExpander
{
    const NAME = 'isUrl';

    private $error;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (false === \is_string($value)) {
            $this->error = \sprintf('IsUrl expander require "string", got "%s".', new StringConverter($value));
            return false;
        }

        if (false === $this->matchValue($value)) {
            $this->error = \sprintf('string "%s" is not a valid URL.', $value);
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
        try {
            return false !== \filter_var($value, FILTER_VALIDATE_URL);
        } catch (\Exception $e) {
            return false;
        }
    }
}
