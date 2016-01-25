<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class Contains implements PatternExpander
{
    /**
     * @var null|string
     */
    private $error;

    /**
     * @var
     */
    private $string;

    /**
     * @var bool
     */
    private $ignoreCase;

    /**
     * @param $string
     * @param bool $ignoreCase
     */
    public function __construct($string, $ignoreCase = false)
    {
        $this->string = $string;
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * @param $value
     * @return boolean
     */
    public function match($value)
    {
        if (!is_string($value)) {
            $this->error = sprintf("Contains expander require \"string\", got \"%s\".", new StringConverter($value));
            return false;
        }

        $contains = $this->ignoreCase
            ? mb_strpos(mb_strtolower($value), mb_strtolower($this->string))
            : mb_strpos($value, $this->string);

        if ($contains === false) {
            $this->error = sprintf("String \"%s\" doesn't contains \"%s\".", $value, $this->string);
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
