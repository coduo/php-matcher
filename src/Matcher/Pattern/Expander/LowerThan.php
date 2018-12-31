<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern\Expander;

use Coduo\PHPMatcher\Matcher\Pattern\PatternExpander;
use Coduo\ToString\StringConverter;

final class LowerThan implements PatternExpander
{
    const NAME = 'lowerThan';

    private $boundary;

    private $error;


    public function __construct($boundary)
    {
        if (!\is_float($boundary) && !\is_integer($boundary) && !\is_double($boundary) && !$this->is_datetime($boundary)) {
            throw new \InvalidArgumentException(\sprintf('Boundary value "%s" is not a valid number nor a date.', new StringConverter($boundary)));
        }

        $this->boundary = $boundary;
    }

    public static function is(string $name) : bool
    {
        return self::NAME === $name;
    }

    public function match($value) : bool
    {
        if (!\is_float($value) && !\is_integer($value) && !\is_double($value) && !$this->is_datetime($value)) {
            $this->error = \sprintf('Value "%s" is not a valid number nor a date.', new StringConverter($value));
            return false;
        }

        if($this->is_datetime($this->boundary) != $this->is_datetime($value)){
            $this->error = \sprintf('Value "%s" is not the same type as "%s", booth must date or a number.', new StringConverter($value), new StringConverter($this->boundary));
            return false;
        }
        
        if($this->is_datetime($this->boundary))
        {
            $this->boundary = new \DateTime($this->boundary);
        }

        if($this->is_datetime($value))
        {
            $value = new \DateTime($value);
        }

        if ($value >= $this->boundary) {
            $this->error = \sprintf('Value "%s" is not lower than "%s".', new StringConverter($value), new StringConverter($this->boundary));
            return false;
        }

        return $value < $this->boundary;
    }

    private function is_datetime($value) : bool
    {
        if(is_string($value) == false) return false;
        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getError()
    {
        return $this->error;
    }
}
