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
            $this->error = sprintf("Invalid given JSON of value. %s", $this->getErrorMessage());
            return false;
        }

        if (!Json::isValidPattern($pattern) ) {
            $this->error = sprintf("Invalid given JSON of pattern. %s", $this->getErrorMessage());
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

    private function getErrorMessage()
    {
        switch(json_last_error()) {
            case JSON_ERROR_DEPTH:
                return 'Maximum stack depth exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Underflow or the modes mismatch';
            case JSON_ERROR_CTRL_CHAR:
                return 'Unexpected control character found';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error, malformed JSON';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            default:
                return 'Unknown error';
        }
    }
}
