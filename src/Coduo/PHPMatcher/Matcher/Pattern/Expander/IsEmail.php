<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class IsEmail implements PatternExpander
{
    /**
     * @var null|string
     */
    private $error;

    /**
     * @param string $value
     * @return boolean
     */
    public function match($value)
    {
        if (false === is_string($value)) {
            $this->error = sprintf("IsEmail expander require \"string\", got \"%s\".", new StringConverter($value));
            return false;
        }

        if (false === $this->matchValue($value)) {
            $this->error = sprintf("string \"%s\" is not a valid e-mail address.", $value);
            return false;
        }

        return true;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $value
     * @return bool
     */
    protected function matchValue($value)
    {
        try {
            return false !== filter_var($value, FILTER_VALIDATE_EMAIL);
        } catch (\Exception $e) {
            return false;
        }
    }
}
