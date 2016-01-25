<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ExpressionMatcher extends Matcher
{
    const MATCH_PATTERN = "/^expr\((.*?)\)$/";

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        $language = new ExpressionLanguage();
        preg_match(self::MATCH_PATTERN, $pattern, $matches);
        $expressionResult = $language->evaluate($matches[1], array('value' => $value));

        if (!$expressionResult) {
            $this->error = sprintf("\"%s\" expression fails for value \"%s\".", $pattern, new StringConverter($value));
        }

        return (bool) $expressionResult;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match(self::MATCH_PATTERN, $pattern);
    }
}
