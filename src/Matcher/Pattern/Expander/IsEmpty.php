<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class IsEmpty implements PatternExpander
{
    const NAME = 'isEmpty';

    private $error;

    /**
     * {@inheritdoc}
     */
    public static function is($name)
    {
        return self::NAME === $name;
    }

    /**
     * @param mixed $value
     *
     * @return boolean
     */
    public function match($value)
    {
        if (!empty($value)) {
            $this->error = sprintf("Value %s is not empty.", new StringConverter($value));

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
}
