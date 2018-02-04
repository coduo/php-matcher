<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class BooleanMatcher extends Matcher
{
    const PATTERN = 'boolean';

    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function match($value, $pattern) : bool
    {
        if (!\is_bool($value)) {
            $this->error = \sprintf('%s "%s" is not a valid boolean.', \gettype($value), new StringConverter($value));
            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        if (!\is_string($pattern)) {
            return false;
        }

        return $this->parser->hasValidSyntax($pattern) && $this->parser->parse($pattern)->is(self::PATTERN);
    }
}
