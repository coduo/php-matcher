<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class IsIp implements PatternExpander
{
    const NAME = 'isIp';

    private $error;

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (false === \is_string($value)) {
            $this->error = \sprintf('IsIp expander require "string", got "%s".', new StringConverter($value));
            return false;
        }

        if (false === $this->matchValue($value)) {
            $this->error = \sprintf('string "%s" is not a valid IP address.', $value);
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
            return false !== \filter_var($value, FILTER_VALIDATE_IP);
        } catch (\Exception $e) {
            return false;
        }
    }
}
