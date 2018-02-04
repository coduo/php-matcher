<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Parser;
use Coduo\ToString\StringConverter;

final class IntegerMatcher extends Matcher
{
    const PATTERN = 'integer';

    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function match($value, $pattern) : bool
    {
        if (!\is_integer($value)) {
            $this->error = \sprintf('%s "%s" is not a valid integer.', \gettype($value), new StringConverter($value));
            return false;
        }

        $typePattern = $this->parser->parse($pattern);
        if (!$typePattern->matchExpanders($value)) {
            $this->error = $typePattern->getError();
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
