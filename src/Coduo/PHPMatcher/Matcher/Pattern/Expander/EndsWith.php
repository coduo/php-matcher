<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class EndsWith implements PatternExpander
{
    /**
     * @var
     */
    private $stringEnding;

    /**
     * @var null|string
     */
    private $error;

    /**
     * @var bool
     */
    private $ignoreCase;

    /**
     * @param string $stringEnding
     * @param bool $ignoreCase
     */
    public function __construct($stringEnding, $ignoreCase = false)
    {
        if (!is_string($stringEnding)) {
            throw new \InvalidArgumentException("String ending must be a valid string.");
        }
        
        $this->stringEnding = $stringEnding;
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * @param $value
     * @return boolean
     */
    public function match($value)
    {
        if (!is_string($value)) {
            $this->error = sprintf("EndsWith expander require \"string\", got \"%s\".", new StringConverter($value));
            return false;
        }

        if (empty($this->stringEnding)) {
            return true;
        }

        if (!$this->matchValue($value)) {
            $this->error = sprintf("string \"%s\" doesn't ends with string \"%s\".", $value, $this->stringEnding);
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
     * @param $value
     * @return bool
     */
    protected function matchValue($value)
    {
        return $this->ignoreCase
            ? mb_substr(mb_strtolower($value), -mb_strlen(mb_strtolower($this->stringEnding))) === mb_strtolower($this->stringEnding)
            : mb_substr($value, -mb_strlen($this->stringEnding)) === $this->stringEnding;
    }
}
