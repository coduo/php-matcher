<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern;

use Coduo\PHPMatcher\Exception\UnknownTypeException;

final class RegexConverter
{
    public function toRegex(TypePattern $typePattern) : string
    {
        switch ($typePattern->getType()) {
            case 'string':
            case 'wildcard':
            case '*':
                return '(.+)';
            case 'number':
                return '(\\-?[0-9]*[\\.|\\,]?[0-9]*)';
            case 'integer':
                return '(\\-?[0-9]*)';
            case 'double':
                return '(\\-?[0-9]*[\\.|\\,][0-9]*)';
            case 'uuid':
                return "([\da-f]{8}-[\da-f]{4}-[1-5][\da-f]{3}-[89ab][\da-f]{3}-[\da-f]{12})";
            default:
                throw new UnknownTypeException($typePattern->getType());
        }
    }
}
