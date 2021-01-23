<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\ToString\StringConverter;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

final class ExpressionMatcher extends Matcher
{
    /**
     * @var string
     */
    public const MATCH_PATTERN = "/^expr\((.*?)\)$/";

    private Backtrace $backtrace;

    public function __construct(Backtrace $backtrace)
    {
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        $language = new ExpressionLanguage();
        \preg_match(self::MATCH_PATTERN, $pattern, $matches);
        $expressionResult = $language->evaluate($matches[1], ['value' => $value]);

        if (!$expressionResult) {
            $this->error = \sprintf('"%s" expression fails for value "%s".', $pattern, new StringConverter($value));
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }
        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        $result = \is_string($pattern) && 0 !== \preg_match(self::MATCH_PATTERN, $pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
