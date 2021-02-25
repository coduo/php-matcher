<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Backtrace;
use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;

final class JsonMatcher extends Matcher
{
    private ArrayMatcher $arrayMatcher;

    private Backtrace $backtrace;

    public function __construct(ArrayMatcher $arrayMatcher, Backtrace $backtrace)
    {
        $this->arrayMatcher = $arrayMatcher;
        $this->backtrace = $backtrace;
    }

    public function match($value, $pattern) : bool
    {
        $this->backtrace->matcherEntrance(self::class, $value, $pattern);

        if (parent::match($value, $pattern)) {
            $this->backtrace->matcherSucceed(self::class, $value, $pattern);

            return true;
        }

        if (!Json::isValid($value)) {
            $this->error = \sprintf('Invalid given JSON of value. %s', Json::getErrorMessage());
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        if (!Json::isValidPattern($pattern)) {
            $this->error = \sprintf('Invalid given JSON of pattern. %s', Json::getErrorMessage());
            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $transformedPattern = Json::isValid($pattern) ? $pattern : Json::transformPattern($pattern);

        $match = $this->arrayMatcher->match(\json_decode($value, true), \json_decode($transformedPattern, true));

        if (!$match) {
            $this->error = $this->arrayMatcher->getError();

            $this->backtrace->matcherFailed(self::class, $value, $pattern, $this->error);

            return false;
        }

        $this->backtrace->matcherSucceed(self::class, $value, $pattern);

        return true;
    }

    public function canMatch($pattern) : bool
    {
        $result = Json::isValidPattern($pattern);
        $this->backtrace->matcherCanMatch(self::class, $pattern, $result);

        return $result;
    }
}
