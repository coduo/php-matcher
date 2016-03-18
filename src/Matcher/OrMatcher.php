<?php

namespace Coduo\PHPMatcher\Matcher;

final class OrMatcher extends Matcher
{
    const MATCH_PATTERN = "/\|\|/";

    /**
     * @var ChainMatcher
     */
    private $chainMatcher;

    /**
     * @param ChainMatcher $chainMatcher
     */
    public function __construct(ChainMatcher $chainMatcher)
    {
        $this->chainMatcher = $chainMatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        $patterns = explode('||', $pattern);
        foreach ($patterns as $childPattern) {
            if ($this->matchChild($value, $childPattern)){
                return true;
            }
        }

        return false;
    }

    /**
     * Matches single pattern
     *
     * @param $value
     * @param $pattern
     * @return bool
     */
    private function matchChild($value, $pattern)
    {
        if (!$this->chainMatcher->canMatch($pattern)) {
            return false;
        }

        if ($this->chainMatcher->match($value, $pattern)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match_all(self::MATCH_PATTERN, $pattern, $matches);
    }
}
