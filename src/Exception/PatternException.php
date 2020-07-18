<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Exception;

class PatternException extends Exception
{
    public static function syntaxError(string $message, Exception $previous = null) : self
    {
        return new self('[Syntax Error] ' . $message, 0, $previous);
    }
}
