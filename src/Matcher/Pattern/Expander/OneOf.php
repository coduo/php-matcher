<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class OneOf implements PatternExpander
{
    const NAME = 'oneOf';

    /**
     * @var PatternExpander[]
     */
    protected $expanders;

    protected $error;

    /**
     * {@inheritdoc}
     */
    public static function is(string $name)
    {
        return self::NAME === $name;
    }

    public function __construct()
    {
        if (func_num_args() < 2) {
            throw new \InvalidArgumentException("OneOf expander require at least two expanders.");
        }
        foreach (func_get_args() as $argument) {
            if (!$argument instanceof PatternExpander) {
                throw new \InvalidArgumentException("OneOf expander require each argument to be a valid PatternExpander.");
            }

            $this->expanders[] = $argument;
        }
    }

    /**
     * @param $value
     * @return boolean
     */
    public function match($value)
    {
        foreach ($this->expanders as $expander) {
            if ($expander->match($value)) {
                return true;
            }
        }

        $this->error = sprintf("Any expander available in OneOf expander does not match \"%s\".", new StringConverter($value));
        return false;
    }

    /**
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }
}
