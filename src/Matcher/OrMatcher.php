<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

final class OrMatcher extends Matcher
{
    const MATCH_PATTERN = "/\|\|/";

    private $chainMatcher;

    public function __construct(ChainMatcher $chainMatcher)
    {
        $this->chainMatcher = $chainMatcher;
    }

    public function match($value, $pattern) : bool
    {
        $patterns = \explode('||', $pattern);
        $patterns = \array_map('trim', $patterns);

        foreach ($patterns as $childPattern) {
            if ($this->matchChild($value, $childPattern)) {
                return true;
            }
        }

        return false;
    }

    private function matchChild($value, $pattern) : bool
    {
        if (!$this->chainMatcher->canMatch($pattern)) {
            return false;
        }

        if ($this->chainMatcher->match($value, $pattern)) {
            return true;
        }

        return false;
    }

    public function canMatch($pattern): bool
    {
        return \is_string($pattern) && 0 !== \preg_match_all(self::MATCH_PATTERN, $pattern, $matches);
    }
}
