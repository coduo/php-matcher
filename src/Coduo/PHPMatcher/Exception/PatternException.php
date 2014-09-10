<?php

namespace Coduo\PHPMatcher\Exception;

class PatternException extends Exception
{
    public static function syntaxError($message, $previous = null)
    {
        return new self('[Syntax Error] ' . $message, 0, $previous);
    }
}
