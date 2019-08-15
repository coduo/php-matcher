<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class IsEmail implements PatternExpander
{
    const NAME = 'isEmail';

    private $error;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (false === \is_string($value)) {
            $this->error = \sprintf('IsEmail expander require "string", got "%s".', new StringConverter($value));
            return false;
        }

        if (false === $this->matchValue($value)) {
            $this->error = \sprintf('string "%s" is not a valid e-mail address.', $value);
            return false;
        }

        return true;
    }

    public function getError() : ?string
    {
        return $this->error;
    }

    private function matchValue(string $value) : bool
    {
        try {
            return false !== \filter_var($value, FILTER_VALIDATE_EMAIL);
        } catch (\Exception $e) {
            return false;
        }
    }
}
