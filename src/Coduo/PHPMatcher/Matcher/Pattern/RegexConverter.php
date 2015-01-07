<?php

namespace Coduo\PHPMatcher\Matcher\Pattern;

use Coduo\PHPMatcher\Exception\UnknownTypeException;

class RegexConverter
{
    public function toRegex(TypePattern $type)
    {
        switch ($type->getType()) {
            case 'string':
            case 'wildcard':
            case '*':
                return "(.+)";
            case 'number':
                return "(\\-?[0-9]*[\\.|\\,]?[0-9]*)";
            case 'integer':
                return "(\\-?[0-9]*)";
            case 'double':
                return "(\\-?[0-9]*[\\.|\\,][0-9]*)";
            case 'null':
                return "(null|NULL)";
            default:
                throw new UnknownTypeException();
        }
    }
}
