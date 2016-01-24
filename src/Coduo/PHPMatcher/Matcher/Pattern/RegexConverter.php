<?php

namespace Coduo\PHPMatcher\Matcher\Pattern;

use Coduo\PHPMatcher\Exception\UnknownTypeException;

final class RegexConverter
{
    public function toRegex(TypePattern $typePattern)
    {
        switch ($typePattern->getType()) {
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
            default:
                throw new UnknownTypeException($typePattern->getType());
        }
    }
}
