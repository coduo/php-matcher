<?php

namespace PHPMatcher\Matcher;

class JsonMatcher implements PropertyMatcher
{
    /**
     * @var
     */
    private $matcher;

    /**
     * @param PropertyMatcher $matcher
     */
    public function __construct(PropertyMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    /**
     * {@inheritDoc}
     */
    public function match($value, $pattern)
    {
        if (!is_string($value) || !$this->isValidJson($value)) {
            return false;
        }

        $pattern = $this->transformPattern($pattern);
        return $this->matcher->match(json_decode($value, true), json_decode($pattern, true));
    }

    /**
     * {@inheritDoc}
     */
    public function canMatch($pattern)
    {
        return is_string($pattern) && $this->isValidJson($pattern);
    }

    private function isValidJson($string)
    {
        @json_decode($string, true);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    private function transformPattern($pattern)
    {
        return preg_replace('/([^"])@(integer|string|array|double|wildcard|boolean)@([^"])/', '$1"@$2@"$3', $pattern);
    }
}
