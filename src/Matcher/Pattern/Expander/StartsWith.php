<?php

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class StartsWith implements PatternExpander
{
    /**
     * @var
     */
    private $stringBeginning;

    /**
     * @var null|string
     */
    private $error;

    /**
     * @var bool
     */
    private $ignoreCase;

    /**
     * @param string $stringBeginning
     * @param bool $ignoreCase
     */
    public function __construct($stringBeginning, $ignoreCase = false)
    {
        if (!is_string($stringBeginning)) {
            throw new \InvalidArgumentException("String beginning must be a valid string.");
        }

        $this->stringBeginning = $stringBeginning;
        $this->ignoreCase = $ignoreCase;
    }

    /**
     * @param $value
     * @return boolean
     */
    public function match($value)
    {
        if (!is_string($value)) {
            $this->error = sprintf("StartsWith expander require \"string\", got \"%s\".", new StringConverter($value));
            return false;
        }

        if (empty($this->stringBeginning)) {
            return true;
        }

        if ($this->matchValue($value)) {
            $this->error = sprintf("string \"%s\" doesn't starts with string \"%s\".", $value, $this->stringBeginning);
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
            ? mb_strpos(mb_strtolower($value), mb_strtolower($this->stringBeginning)) !== 0
            : mb_strpos($value, $this->stringBeginning) !== 0;
    }
}
