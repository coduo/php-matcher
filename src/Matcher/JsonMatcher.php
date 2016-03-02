<?php

namespace Coduo\PHPMatcher\Matcher;

use Coduo\PHPMatcher\Matcher\Pattern\Assert\Json;

final class JsonMatcher extends Matcher
{
    /**
     * @var
     */
    private $matcher;

    /**
     * @param ValueMatcher $matcher
     */
    public function __construct(ValueMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (parent::match($value, $pattern)) {
            return true;
        }

        if (!Json::isValid($value)) {
            return false;
        }

        $transformedPattern = Json::transformPattern($pattern);
        $match = $this->matcher->match(json_decode($value, true), json_decode($transformedPattern, true));
        if (!$match) {
            $this->error = $this->matcher->getError();
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return Json::isValidPattern($pattern);
    }
}
