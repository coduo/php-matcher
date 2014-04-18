<?php

namespace PHPMatcher\Matcher;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionMatcher implements PropertyMatcher
{
    const MATCH_PATTERN = "/^expr\((.*?)\)$/";

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        $language = new ExpressionLanguage();
        preg_match(self::MATCH_PATTERN, $pattern, $matches);

        return $language->evaluate($matches[1], array('value' => $value));
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::MATCH_PATTERN, $pattern);
    }
}
