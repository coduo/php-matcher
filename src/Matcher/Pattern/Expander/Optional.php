<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;

final class Optional implements PatternExpander
{
    const NAME = 'optional';

    /**
     * {@inheritdoc}
     */
    public static function is($name)
    {
        return self::NAME === $name;
    }

    /**
     * {@inheritdoc}
     */
    public function match($value)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getError()
    {
        return null;
    }
}
