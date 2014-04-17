<?php

namespace JsonMatcher\Matcher;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionMatcher implements PropertyMatcher
{
    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        $language = new ExpressionLanguage();
        preg_match("/^expr\((.*?)\)$/", $pattern, $matches);
        return $language->evaluate($matches[1], array('value' => $value));
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && 0 !== preg_match("/^expr\((.*?)\)$/", $pattern);
    }
}
