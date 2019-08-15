<?php

declare(strict_types=1);

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;

final class JsonMatcher extends Matcher
{
    private $arrayMatcher;

    public function __construct(ArrayMatcher $arrayMatcher)
    {
        $this->arrayMatcher = $arrayMatcher;
    }

    public function match($value, $pattern) : bool
    {
        if (parent::match($value, $pattern)) {
            return true;
        }

        if (!Json::isValid($value)) {
            $this->error = \sprintf('Invalid given JSON of value. %s', Json::getErrorMessage());
            return false;
        }

        if (!Json::isValidPattern($pattern)) {
            $this->error = \sprintf('Invalid given JSON of pattern. %s', Json::getErrorMessage());
            return false;
        }

        $transformedPattern = Json::transformPattern($pattern);

        $match = $this->arrayMatcher->match(\json_decode($value, true), \json_decode($transformedPattern, true));

        if (!$match) {
            $this->error = $this->arrayMatcher->getError();

            return false;
        }

        return true;
    }

    public function canMatch($pattern) : bool
    {
        return Json::isValidPattern($pattern);
    }
}
