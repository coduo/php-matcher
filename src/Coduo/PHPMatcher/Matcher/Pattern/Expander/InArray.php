<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\String;

class InArray implements PatternExpander
{
    /**
     * @var null|string
     */
    private $error;

    /**
     * @var
     */
    private $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @param $value
     * @return boolean
     */
    public function match($value)
    {
        if (!is_array($value)) {
            $this->error = sprintf("InArray expander require \"array\", got \"%s\".", new String($value));
            return false;
        }

        if (!in_array($this->value, $value, true)) {
            $this->error = sprintf("%s doesn't have \"%s\" element.", new String($value), new String($this->value));
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
