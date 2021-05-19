<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher\Pattern;

use Coduo\PHPMatcher\Exception\UnknownTypeException;
use Coduo\PHPMatcher\Matcher\UlidMatcher;
use Coduo\PHPMatcher\Matcher\UuidMatcher;

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
                return '(' . UuidMatcher::UUID_PATTERN . ')';
            case 'ulid':
                return '(' . UlidMatcher::ULID_PATTERN . ')';

            default:
                throw new UnknownTypeException($typePattern->getType());
        }
    }
}
