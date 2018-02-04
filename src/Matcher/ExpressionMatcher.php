<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\ToString\StringConverter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ExpressionMatcher extends Matcher
{
    const MATCH_PATTERN = "/^expr\((.*?)\)$/";

    public function match($value, $pattern) : bool
    {
        $language = new ExpressionLanguage();
        \preg_match(self::MATCH_PATTERN, $pattern, $matches);
        $expressionResult = $language->evaluate($matches[1], ['value' => $value]);

        if (!$expressionResult) {
            $this->error = \sprintf('"%s" expression fails for value "%s".', $pattern, new StringConverter($value));
        }

        return (bool) $expressionResult;
    }

    public function canMatch($pattern) : bool
    {
        return \is_string($pattern) && 0 !== \preg_match(self::MATCH_PATTERN, $pattern);
    }
}
